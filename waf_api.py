import sys
import json
import os
import re
import urllib.parse
import html
import numpy as np
from flask import Flask, request, jsonify

os.environ["TF_CPP_MIN_LOG_LEVEL"] = "3"
import tensorflow as tf
from tensorflow.keras.preprocessing.sequence import pad_sequences
import pickle

SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_PATH = os.path.join(SCRIPT_DIR, "hybrid_waf_model.keras")
TOKNZ_PATH = os.path.join(SCRIPT_DIR, "waf_tokenizer.pickle")
MAX_LEN = 150

app = Flask(__name__)

print("🚀 Loading AI WAF Model...")
try:
    with open(TOKNZ_PATH, "rb") as fh:
        tokenizer = pickle.load(fh)
    model = tf.keras.models.load_model(MODEL_PATH)
    print("✅ Model & Tokenizer loaded successfully!")
except Exception as e:
    print(f"❌ Failed to load model: {e}")
    sys.exit(1)

CLASS_MAP = {
    0: {"is_attack": False, "attack_type": "Normal", "severity": "low"},
    1: {"is_attack": True, "attack_type": "SQL Injection", "severity": "critical"},
    2: {"is_attack": True, "attack_type": "XSS", "severity": "critical"},
    3: {"is_attack": True, "attack_type": "Path Traversal", "severity": "critical"},
}

def clean_and_decode(text: str) -> str:
    if not isinstance(text, str): 
        return ""
    
    # 1. فك التشفير الأساسي
    text = urllib.parse.unquote(text)
    text = urllib.parse.unquote(text)
    text = html.unescape(text)
    text = text.lower()
    
    # 2. التعديل الجوهري: إزالة تعليقات MySQL الشرطية قبل أن يراها الموديل
    # هذا يمنع الموديل من الخلط بين /*!50000union*/ وبين Path Traversal
    text = re.sub(r'/\*![\d\s]*\*/', ' ', text) 
    text = re.sub(r'/\*.*?\*/', ' ', text)
    
    # 3. عزل الرموز بشكل واضح للـ Tokenizer (بدون حذف)
    # الـ SQLi يعتمد على وجود الرموز في سياق، لذا لا تحذفها
    text = re.sub(r'([<>\'\"()=,;/\\#\-\+\*\!])', r' \1 ', text)
    
    # 4. تنظيف الـ URLs وتحويلها لـ Tokens موحدة
    text = re.sub(r'https?://\S+', ' url_link ', text)
    
    # 5. تقليل المسافات الزائدة
    text = re.sub(r'\s+', ' ', text)
    
    return text.strip()

def flatten_payload(request_data) -> str:
    parts = []
    def extract_values(obj):
        if obj is None: return
        if isinstance(obj, dict):
            for k, v in obj.items():
                parts.append(str(k))
                extract_values(v)
        elif isinstance(obj, list):
            for item in obj: extract_values(item)
        else:
            parts.append(str(obj))
            
    if isinstance(request_data, dict):
        for namespace in ("get", "post"):
            if namespace in request_data:
                extract_values(request_data[namespace])
    else:
        extract_values(request_data)
    return " ".join(parts)

@app.route('/predict', methods=['POST'])
def predict():
    try:
        payload = request.get_json(force=True, silent=True)
        if not payload or not isinstance(payload, dict):
            return jsonify({"is_attack": False, "attack_type": "Normal", "severity": "low", "confidence": 0.0}), 400

        request_data = payload.get("request_data", {})
        get_data = request_data.get("get", {})
        
        # القائمة البيضاء لصفحات لوحة التحكم
        if isinstance(get_data, dict) and get_data.get("section") in ["users", "courses", "security", "analytics", "dashboard"] and len(get_data) == 1:
            return jsonify({"is_attack": False, "attack_type": "Normal", "severity": "low", "confidence": 100.0})

        flattened = flatten_payload(request_data)
        cleaned_data = clean_and_decode(flattened)
        
        # تحويل لـ string صريح وضمان إنه مش فاضي عشان الـ Regex ميتجننش
        cleaned_data = str(cleaned_data).strip()

        if not cleaned_data or len(cleaned_data) < 2:
            return jsonify({"is_attack": False, "attack_type": "Normal", "severity": "low", "confidence": 0.0})

        # تشغيل الموديل
        sequence = tokenizer.texts_to_sequences([cleaned_data])
        padded = pad_sequences(sequence, maxlen=MAX_LEN, padding="post", truncating="post")

        probs = model.predict(padded, verbose=0)[0]
        pred_class = int(np.argmax(probs))
        confidence = float(probs[pred_class])

        info = CLASS_MAP.get(pred_class, {"is_attack": False, "attack_type": "Normal", "severity": "low"})

        # الفلتر الصارم للـ SQL Injection (حماية مضافة ومؤمنة ضد الكراش)
        if info["is_attack"] and info["attack_type"] == "SQL Injection":
            sql_patterns = [r"'", r'"', r"=", r"--", r"\bor\b", r"\band\b", r"union", r"select"]
            # تأكيد الفحص بأمان
            has_pattern = any(re.search(pattern, cleaned_data, re.IGNORECASE) for pattern in sql_patterns)
            if not has_pattern:
                info = CLASS_MAP[0] # اقلبها طبيعي لو مفيش رموز حقيقية
                confidence = float(probs[0])

        if info["is_attack"] and confidence < 0.85:
            info = CLASS_MAP[0]
            confidence = float(probs[0])

        return jsonify({
            "is_attack": info["is_attack"],
            "attack_type": info["attack_type"],
            "severity": info["severity"],
            "confidence": round(confidence * 100, 2)
        })
    except Exception as e:
        # لو حصل أي مصيبة جوه، اطبع الـ Error صريح في الـ CMD عشان نشوفه بدل الـ 500 الصامتة
        print(f"🚨 CRITICAL EXPERT ERROR: {str(e)}")
        return jsonify({"is_attack": False, "attack_type": "Error", "severity": "low", "confidence": 0.0}), 500
    
if __name__ == "__main__":
    app.run(host='127.0.0.1', port=5005, debug=True, threaded=True)