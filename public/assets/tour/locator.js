async function setupCamera() {
  const video = document.getElementById('camera');
  const stream = await navigator.mediaDevices.getUserMedia({ video: true });
  video.srcObject = stream;
  return new Promise((resolve) => {
    video.onloadedmetadata = () => {
      resolve(video);
    };
  });
}

async function loadImages() {
  const response = await fetch('locateImg.php');
  const imagePaths = await response.json();
  return imagePaths;
}

async function extractFeatures(model, tensor) {
  return tf.tidy(() => {
    const resized = tf.image.resizeBilinear(tensor, [224, 224]);
    const normalized = resized.div(255.0);
    return model.predict(normalized);
  });
}

async function compareImages(cameraFrame, imagePath, model) {
  const imageTensor = await loadImageAsTensor(imagePath);
  const cameraFeatures = await extractFeatures(model, cameraFrame);
  const imageFeatures = await extractFeatures(model, imageTensor);
  
  const distance = tf.tidy(() => {
    const diff = cameraFeatures.sub(imageFeatures);
    const norm = tf.norm(diff);
    return norm.dataSync()[0];
  });

  tf.dispose([imageTensor, cameraFeatures, imageFeatures]);

  return distance;
}

async function loadImageAsTensor(path) {
  const img = new Image();
  img.src = path;
  await new Promise((resolve) => {
    img.onload = resolve;
  });
  return tf.tidy(() => {
    return tf.browser.fromPixels(img).toFloat().expandDims(0);
  });
}

async function main() {
  const video = await setupCamera();
  const imagePaths = await loadImages();
  const model = await tf.loadGraphModel('https://tfhub.dev/google/tfjs-model/imagenet/mobilenet_v2_140_224/classification/3/default/1', {fromTFHub: true});
  const resultDiv = document.getElementById('result');
  const matchedImage = document.getElementById('matched-image');
  const imageName = document.getElementById('image-name');
  const detectButton = document.getElementById('detect-button');

  video.addEventListener('play', async () => {
    while (true) {
      const cameraFrame = tf.tidy(() => {
        return tf.browser.fromPixels(video).toFloat().expandDims(0);
      });

      let minDistance = Infinity;
      let bestMatch = null;

      for (const path of imagePaths) {
        const distance = await compareImages(cameraFrame, path, model);
        if (distance < minDistance) {
          minDistance = distance;
          bestMatch = path;
        }
      }

      if (bestMatch) {
        matchedImage.src = bestMatch;
        imageName.textContent = `Matched: ${bestMatch}`;
        resultDiv.style.display = 'block';
      }

      tf.dispose(cameraFrame);
      await tf.nextFrame();
    }
  });

  detectButton.addEventListener('click', async () => {
    const cameraFrame = tf.tidy(() => {
      return tf.browser.fromPixels(video).toFloat().expandDims(0);
    });

    let minDistance = Infinity;
    let bestMatch = null;

    for (const path of imagePaths) {
      const distance = await compareImages(cameraFrame, path, model);
      if (distance < minDistance) {
        minDistance = distance;
        bestMatch = path;
      }
    }

    if (bestMatch) {
      matchedImage.src = bestMatch;
      imageName.textContent = `Matched: ${bestMatch}`;
      resultDiv.style.display = 'block';
    }

    tf.dispose(cameraFrame);
  });
}

main();
