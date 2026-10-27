"""Cut the photo out of an Instagram post screenshot (iPhone, 1179px wide) and paint out
the IG overlays (n/8 badge, mute icon, tag icon). Usage: crop_screenshot.py shot.png name
"""
import os
import sys

import numpy as np
from PIL import Image

TOP, BOTTOM = 501, 1385
OVERLAYS = [(1025, 535, 1145, 628), (1050, 1255, 1145, 1350), (30, 1255, 130, 1350)]


def inpaint(a, box, iters=1200, pad=12):
    x0, y0, x1, y1 = box
    X0, Y0, X1, Y1 = max(x0 - pad, 0), max(y0 - pad, 0), min(x1 + pad, a.shape[1]), min(y1 + pad, a.shape[0])
    sub = a[Y0:Y1, X0:X1].copy()
    m = np.zeros(sub.shape[:2], bool)
    m[y0 - Y0:y1 - Y0, x0 - X0:x1 - X0] = True
    sub[m] = sub[~m].mean(0)
    for _ in range(iters):
        s = (np.roll(sub, 1, 0) + np.roll(sub, -1, 0) + np.roll(sub, 1, 1) + np.roll(sub, -1, 1)) / 4
        sub[m] = s[m]
    a[Y0:Y1, X0:X1] = sub


def main(shot, name):
    a = np.asarray(Image.open(shot).convert("RGB").crop((0, TOP, 1179, BOTTOM))).astype(float)
    for x0, y0, x1, y1 in OVERLAYS:
        inpaint(a, (x0, y0 - TOP, x1, y1 - TOP))
    out = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "source-photos", name + ".png")
    Image.fromarray(a.clip(0, 255).astype(np.uint8)).save(out)
    print(name, out)


if __name__ == "__main__":
    main(sys.argv[1], sys.argv[2])
