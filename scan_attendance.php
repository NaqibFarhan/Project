<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SmartTrack QR Scanner</title>
  <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      text-align: center;
      background: #f8f9fa;
      padding: 0;
      margin: 0;
    }

    h1 {
      color: #800000;
      padding: 20px;
    }

    video {
      width: 100%;
      max-width: 500px;
      border: 3px solid #800000;
      border-radius: 10px;
    }

    #message {
      margin-top: 20px;
      font-weight: bold;
      color: #333;
    }
  </style>
</head>
<body>

  <h1> SmartTrack Attendance</h1>
  <video id="video" autoplay></video>
  <canvas id="canvas" style="display:none;"></canvas>
  <div id="message">Scan the QR Attendance code</div>

  <script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const message = document.getElementById('message');

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
      .then((stream) => {
        video.srcObject = stream;
        video.setAttribute("playsinline", true); // for iOS
        video.play();
        requestAnimationFrame(tick);
      })
      .catch((err) => {
        message.textContent = "Camera access error: " + err.message;
      });

    function tick() {
      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, canvas.width, canvas.height);

        if (code) {
          message.textContent = "QR code detected! Processing...";
          const qrUrl = code.data;

          // Redirect to the PHP handler
          window.location.href = qrUrl; // e.g., scan_attendance.php?student_id=123
          return;
        }
      }
      requestAnimationFrame(tick);
    }
  </script>
</body>
</html>
