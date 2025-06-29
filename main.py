from fastapi import FastAPI
from fastapi.responses import StreamingResponse, JSONResponse
import cv2
from ultralytics import YOLO
import numpy as np

app = FastAPI()

# Load YOLOv12 model
model = YOLO('your_model.pt')  # Replace with your YOLOv12 model file

# Class names in order
class_names = ["Drop", "Pick", "Ibrahim", "B1", "B2", "B3", "B4", "B5"]

# Stream URL from Android IP Webcam app (Change IP to your phone's IP)
stream_url = 'http://192.168.0.109:8080/'  # Example IP
cap = cv2.VideoCapture(stream_url)


def get_top_3_detections(result):
    """
    Get top 3 detections based on confidence.
    """
    boxes = result.boxes
    detections = []

    if boxes is not None:
        for box in boxes:
            cls_id = int(box.cls[0])
            confidence = float(box.conf[0])
            xyxy = box.xyxy[0].cpu().numpy().tolist()

            detections.append({
                "class_id": cls_id,
                "class_name": class_names[cls_id],
                "confidence": round(confidence, 2),
                "bbox": [round(v, 2) for v in xyxy]
            })

    # Sort by confidence descending
    detections = sorted(detections, key=lambda x: x['confidence'], reverse=True)

    # Return top 3
    return detections[:3]


def gen_frames():
    while True:
        success, frame = cap.read()
        if not success:
            break

        # YOLO detection
        results = model.predict(frame, imgsz=640, verbose=False)
        result = results[0]

        # Annotated frame
        annotated_frame = result.plot()

        ret, buffer = cv2.imencode('.jpg', annotated_frame)
        frame_bytes = buffer.tobytes()

        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + frame_bytes + b'\r\n')


@app.get('/')
def home():
    return {"message": "YOLOv12 Video Detection API is running"}


@app.get('/video_feed')
def video_feed():
    """
    Stream annotated video with bounding boxes.
    """
    return StreamingResponse(gen_frames(),
                             media_type='multipart/x-mixed-replace; boundary=frame')


@app.get('/get_top3')
def get_top3():
    """
    Return top 3 detections from the current frame.
    """
    success, frame = cap.read()
    if not success:
        return JSONResponse(content={"error": "Failed to read frame"}, status_code=500)

    results = model.predict(frame, imgsz=640, verbose=False)
    result = results[0]

    detections = get_top_3_detections(result)

    return {"top3_detections": detections}
