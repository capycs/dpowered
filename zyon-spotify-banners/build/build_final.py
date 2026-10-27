"""Final hung banner, 6 x 3 ft (1830 x 915 mm), built to outdoor-banner rules:
<= 7 words of primary copy, one focal point, 50 mm safe zone for hems/eyelets,
QR ~1/10 of scan distance (250 mm -> ~2.5 m), photo layers at 150 dpi.
Run: python3 build_final.py && node render.js final_
"""
import numpy as np
from PIL import Image

from build import GREEN, BLACK, WHITE, qr, spotify_icon
from build_v2 import grain_href, grain_layer, image, load, nsmooth, page, place, progress, save, shadow, t
from build_v3 import bw, rings, sticker_alpha

W, H = 1830, 915
SAFE = 50


def eyelet_guides():
    xs = np.linspace(20, W - 20, 5)
    ys = np.linspace(20, H - 20, 3)
    pts = [(x, y) for x in xs for y in (20, H - 20)] + [(x, y) for x in (20, W - 20) for y in ys[1:-1]]
    return "".join(f'<circle cx="{x:.1f}" cy="{y:.1f}" r="6" stroke="#ff0000"/>' for x, y in pts)


def banner(B):
    im, m = load("4_closeup", choke=3)
    scale = 1.2
    # tuck the cap brim ~45 mm over the right edge of the N
    ma = np.asarray(m) > 128
    rows = ma[150 * 4:380 * 4]
    brim_x = np.where(rows.any(0))[0].min() / 4
    name_right = SAFE + 965
    off = (name_right - 45 - brim_x * scale, -20)
    mic = (off[0] + 640 * scale, off[1] + 520 * scale)

    rect = (700, -B, W + B - 700, H + 2 * B)
    src = place(im, scale, off, rect, 150)
    a = np.asarray(place(m, scale, off, rect, 150)).astype(float) / 255
    # trim the shoulder (cut by the photo's left edge) along an arc around the mic, echoing the rings
    h, w = a.shape
    xs = rect[0] + (np.arange(w) + 0.5) * rect[2] / w
    ys = rect[1] + (np.arange(h) + 0.5) * rect[3] / h
    d = np.hypot(xs[None, :] - mic[0], ys[:, None] - mic[1])
    lower_left = (ys[:, None] > mic[1] - 160) & (xs[None, :] < mic[0])
    a *= 1 - nsmooth(557, 563, d) * lower_left

    rgb = bw(src, a, s_curve=0.6)
    border = sticker_alpha(a, border_px=55)
    comp = rgb * a[..., None] + 1.0 * (1 - a[..., None])          # subject over white sticker border
    subj = save(comp, f"final_subject_b{B}", border)
    small = np.asarray(Image.fromarray((border * 255).astype(np.uint8)).reduce(2)).astype(float) / 255
    shd = shadow(small, f"final_shadow_b{B}", blur_px=10, strength=0.5)

    body = f"""
<rect x="{-B}" y="{-B}" width="{W + 2 * B}" height="{H + 2 * B}" fill="{GREEN}"/>
{rings(*mic, [150, 270, 400, 540, 690, 850, 1020, 1200, 1390], 30, BLACK, 0.13)}
{t(SAFE, 0, "ZYON", 500, BLACK, "Anton", 400, ls=3, fit=965, captop=58)}
{image(shd, (rect[0] - 10, rect[1] + 10, rect[2], rect[3]))}
{image(subj, rect)}
{grain_layer(grain_href(), B, W, H, opacity=0.4)}
{spotify_icon(SAFE, 530, 80, BLACK)}
{t(SAFE + 98, 0, "LISTEN ON SPOTIFY", 60, BLACK, "Archivo", 400, fit=650, captop=549)}
{qr(SAFE, 615, 250, BLACK, WHITE)}
<path d="M366 641H337M347 631L336 641L347 651" fill="none" stroke="{BLACK}" stroke-width="3.6"/>
{t(378, 650, "SCAN TO STREAM", 26, BLACK, "Mono", ls=2)}
{t(332, 0, "@zyon_333", 60, BLACK, fit=440, captop=676)}
{progress(334, 790, 456, color=BLACK, rest="#0d6b33", h=6, knob=9, times_y=822, tcolor=BLACK, tsize=19)}
{t(332, 862, "PHOTO @FRAMED.BY.ICARUS", 13, BLACK, weight=500, ls=1, extra='opacity="0.7"')}
"""
    guides = (f'<rect x="{SAFE}" y="{SAFE}" width="{W - 2 * SAFE}" height="{H - 2 * SAFE}" stroke="#00ffff" stroke-dasharray="10 6"/>'
              + eyelet_guides())
    page(f"final_ZYON_banner_1830x915mm_bleed{B}", W, H, B, body, guides)


if __name__ == "__main__":
    for b in (5, 25):
        banner(b)
        print("built bleed", b)
