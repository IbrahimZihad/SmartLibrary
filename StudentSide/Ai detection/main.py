from flask import Flask, jsonify, Response, render_template
from ultralytics import YOLO
import cv2
import json
import os

app = Flask(__name__)
model = YOLO("best.pt")  # Replace with your trained model path
cart = set()

# Replace with your phone's IP Webcam stream URL
VIDEO_URL = "F:\\Xamp\\htdocs\\Ai detection\\pickbook5_detected_30fps.mp4"

def process_frame():
    global cart
    cap = cv2.VideoCapture(VIDEO_URL)

    while True:
        success, frame = cap.read()
        if not success:
            continue

        results = model(frame)
        names = model.names

        # Collect all detections
        detections = []
        for r in results:
            for box in r.boxes:
                cls_id = int(box.cls[0])
                label = names[cls_id]
                coords = box.xywh[0].tolist()
                confidence = float(box.conf[0])

                detections.append({
                    "class": label,
                    "confidence": confidence,
                    "bbox_xywh": coords
                })

        # Get top 3 unique classes by confidence
        seen_classes = set()
        top3_detections = []

        for det in sorted(detections, key=lambda x: x["confidence"], reverse=True):
            if det["class"] not in seen_classes:
                top3_detections.append({
                    "class": det["class"],
                    "confidence": round(det["confidence"], 3),
                    "bbox_xywh": [round(c, 2) for c in det["bbox_xywh"]]
                })
                seen_classes.add(det["class"])
            if len(top3_detections) == 3:
                break

        current_classes = {d["class"] for d in top3_detections}

        # Save to detections.json
        with open("detections.json", "w") as f:
            json.dump(top3_detections, f, indent=2)

        # Update cart based on rules
        if "Ibrahim" in current_classes and "Pick" in current_classes:
            for b in ["B1", "B2", "B3", "B4", "B5"]:
                if b in current_classes:
                    cart.add(b)
        elif "Drop" in current_classes:
            for b in ["B1", "B2", "B3", "B4", "B5"]:
                cart.discard(b)

        # Save cart to cart.json
        with open("cart.json", "w") as f:
            json.dump(list(cart), f, indent=2)

        # Annotated frame
        annotated = results[0].plot()
        _, jpeg = cv2.imencode('.jpg', annotated)
        frame = jpeg.tobytes()

        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')


@app.route('/video')
def video_feed():
    return Response(process_frame(), mimetype='multipart/x-mixed-replace; boundary=frame')


@app.route('/cart')
def get_cart():
    if os.path.exists("cart.json"):
        with open("cart.json", "r") as f:
            data = json.load(f)
        return jsonify(data)
    return jsonify([])


@app.route('/detections')
def get_detections():
    if os.path.exists("detections.json"):
        with open("detections.json", "r") as f:
            data = json.load(f)
        return jsonify(data)
    return jsonify([])


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
