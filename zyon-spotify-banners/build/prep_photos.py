"""4x AI upscale (Real-ESRGAN x4plus) + subject mask (BiRefNet) for each source photo.

Models are not in the repo. Download into $MODELS (default: ./models):
  RealESRGAN_x4plus.pth        github.com/xinntao/Real-ESRGAN/releases/download/v0.1.0/
  BiRefNet-general-epoch_244.onnx  github.com/danielgatis/rembg/releases/download/v0.0.0/
Needs: pip install torch spandrel rembg onnxruntime
"""
import os
import sys

import numpy as np
import torch
from PIL import Image, ImageFilter
from spandrel import ModelLoader

HERE = os.path.dirname(os.path.abspath(__file__))
SRC = os.path.join(HERE, "..", "source-photos")
OUT = os.path.join(SRC, "hires")
MODELS = os.environ.get("MODELS", os.path.join(HERE, "models"))
os.makedirs(OUT, exist_ok=True)
torch.set_num_threads(os.cpu_count())


def upscale(img, model, tile=256, pad=16):
    a = torch.from_numpy(np.asarray(img).astype(np.float32) / 255).permute(2, 0, 1)[None]
    _, _, h, w = a.shape
    out = torch.zeros(1, 3, h * 4, w * 4)
    with torch.inference_mode():
        for y in range(0, h, tile):
            for x in range(0, w, tile):
                y0, x0 = max(y - pad, 0), max(x - pad, 0)
                y1, x1 = min(y + tile + pad, h), min(x + tile + pad, w)
                r = model(a[:, :, y0:y1, x0:x1])
                ty, tx = (y - y0) * 4, (x - x0) * 4
                th, tw = (min(y + tile, h) - y) * 4, (min(x + tile, w) - x) * 4
                out[:, :, y * 4:y * 4 + th, x * 4:x * 4 + tw] = r[:, :, ty:ty + th, tx:tx + tw]
    return Image.fromarray((out[0].permute(1, 2, 0).clamp(0, 1).numpy() * 255).round().astype(np.uint8))


def main(names):
    model = ModelLoader().load_from_file(os.path.join(MODELS, "RealESRGAN_x4plus.pth")).eval()
    from rembg import new_session, remove
    os.environ.setdefault("U2NET_HOME", MODELS)
    seg = new_session("birefnet-general")
    for n in names:
        im = Image.open(os.path.join(SRC, n + ".png")).convert("RGB")
        big = upscale(im, model.model)
        big.save(os.path.join(OUT, n + "_x4.jpg"), quality=95)
        mask = remove(im, session=seg, only_mask=True).resize(big.size, Image.LANCZOS)
        mask.filter(ImageFilter.GaussianBlur(1.5)).save(os.path.join(OUT, n + "_mask.png"))
        print(n, big.size, flush=True)


if __name__ == "__main__":
    main(sys.argv[1:] or ["1_cutout", "2_crowd", "3_stage_bw", "4_closeup"])
