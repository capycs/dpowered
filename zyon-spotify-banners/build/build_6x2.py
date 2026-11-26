"""Hung banner, 6 x 2 ft (1830 x 610 mm, 3:1), read left to right:
name -> face (mirrored so he shouts toward the call to action) -> black 'scan me' card.
Same outdoor-banner rules as build_final.py: <= 8 words, 50 mm hem/eyelet safe zone,
240 mm QR (~2.4 m scan), photo at 150 dpi.
Run: python3 build_6x2.py && node flatten.js zbg_6x2 img/f62_flat.jpg 150 && node render.js final_ZYON_banner_6x2
"""
import numpy as np
from PIL import Image, ImageOps

from build import GREEN, BLACK, WHITE, qr, spotify_icon
from build_v2 import grain_href, grain_layer, image, load, nsmooth, page, place, progress, save, shadow, t
from build_v3 import bw, rings, sticker_alpha

W, H, B = 1830, 610, 5
SAFE = 50
DEEP = "#0B7A35"   # hard-shadow green


def eyelet_guides():
    xs = np.linspace(20, W - 20, 5)
    pts = [(x, y) for x in xs for y in (20, H - 20)] + [(20, H / 2), (W - 20, H / 2)]
    return "".join(f'<circle cx="{x:.1f}" cy="{y:.1f}" r="6" stroke="#ff0000"/>' for x, y in pts)


def banner():
    im, m = load("4_closeup", choke=3)
    im, m = ImageOps.mirror(im), ImageOps.mirror(m)          # face right, toward the QR card
    ma = np.asarray(m) > 128
    hair_x = np.where(ma[200 * 4:800 * 4].any(0))[0].min() / 4   # leftmost hair (orig px, mirrored)
    cap_top = np.where(ma.any(1))[0].min() / 4

    scale = 0.8
    off = (838 - hair_x * scale, 38 - cap_top * scale)
    mic = (off[0] + (1179 - 640) * scale, off[1] + 520 * scale)

    rect = (790, -B, W + B - 790, H + 2 * B)
    src = place(im, scale, off, rect, 150)
    a = np.asarray(place(m, scale, off, rect, 150)).astype(float) / 255
    # the photo's frame cuts his shoulder; trim it along an arc around the mic, echoing the rings
    h, w = a.shape
    xs = rect[0] + (np.arange(w) + 0.5) * rect[2] / w
    ys = rect[1] + (np.arange(h) + 0.5) * rect[3] / h
    d = np.hypot(xs[None, :] - mic[0], ys[:, None] - mic[1])
    lower_right = (ys[:, None] > mic[1] - 120) & (xs[None, :] > mic[0])
    a *= 1 - nsmooth(377, 383, d) * lower_right
    rgb = bw(src, a, s_curve=0.6)
    border = sticker_alpha(a, border_px=50)
    comp = rgb * a[..., None] + 1.0 * (1 - a[..., None])         # subject over white sticker border
    subj = save(comp, "f62_subject", border)
    small = np.asarray(Image.fromarray((border * 255).astype(np.uint8)).reduce(2)).astype(float) / 255
    shd = shadow(small, "f62_shadow", blur_px=9, strength=0.5)

    card = (1490, SAFE, 290, H - 2 * SAFE)
    cx, cy, cw, ch = card
    ix, iw = cx + 25, cw - 50

    # Photo, shadows, rings, glow and grain go into ONE flat 150 dpi JPEG (flatten.js renders zbg_6x2.html);
    # type, card and QR stay vector on top. A plain JPEG + vectors opens in any PDF viewer and RIP.
    bg_layers = f"""
<defs>
  <radialGradient id="bg" cx="{mic[0] - 40}" cy="{mic[1] - 120}" r="1150" gradientUnits="userSpaceOnUse">
    <stop offset="0" stop-color="#3CEB86"/>
    <stop offset="0.45" stop-color="{GREEN}"/>
    <stop offset="1" stop-color="#14A94C"/>
  </radialGradient>
</defs>
<rect x="{-B}" y="{-B}" width="{W + 2 * B}" height="{H + 2 * B}" fill="url(#bg)"/>
{rings(*mic, [120, 215, 320, 435, 560, 695, 840, 995, 1160, 1335], 24, BLACK, 0.12)}
{image(shd, (rect[0] + 9, rect[1] + 10, rect[2], rect[3]))}
{image(subj, rect)}
{grain_layer(grain_href(), B, W, H, opacity=0.4)}
"""
    page("zbg_6x2", W, H, B, bg_layers)

    body = f"""
{image("img/f62_flat.jpg", (-B, -B, W + 2 * B, H + 2 * B))}
{t(SAFE + 8, 0, "ZYON", 400, DEEP, "Anton", 400, ls=3, fit=760, captop=70)}
{t(SAFE, 0, "ZYON", 400, BLACK, "Anton", 400, ls=3, fit=760, captop=62)}
{spotify_icon(SAFE, 442, 62, BLACK)}
{t(SAFE + 78, 0, "LISTEN ON SPOTIFY", 60, BLACK, "Archivo", 400, fit=682, captop=452)}
<rect x="{cx + 8}" y="{cy + 8}" width="{cw}" height="{ch}" rx="18" fill="{DEEP}"/>
<rect x="{cx}" y="{cy}" width="{cw}" height="{ch}" rx="18" fill="{BLACK}"/>
{t(ix, 0, "SCAN TO STREAM", 60, WHITE, "Anton", 400, ls=1, fit=iw, captop=cy + 26)}
{qr(ix, cy + 80, iw, BLACK, WHITE)}
{progress(ix + 2, cy + 352, iw - 4, color=GREEN, rest="#3a3a3a", h=5, knob=8, times_y=cy + 378, tsize=14)}
{spotify_icon(ix, cy + 398, 40, GREEN)}
{t(ix + 52, 0, "@zyon_333", 40, WHITE, fit=iw - 52, captop=cy + 408)}
{t(ix, cy + ch - 20, "PHOTO @FRAMED.BY.ICARUS", 8.5, "#8a8a8a", weight=500, ls=0.6)}
"""
    guides = (f'<rect x="{SAFE}" y="{SAFE}" width="{W - 2 * SAFE}" height="{H - 2 * SAFE}" stroke="#00ffff" stroke-dasharray="10 6"/>'
              + eyelet_guides())
    page("final_ZYON_banner_6x2ft_1830x610mm", W, H, B, body, guides)


if __name__ == "__main__":
    banner()
    print("built 6x2")
