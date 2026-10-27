"""Generates the ZYON Spotify banner artwork as HTML/SVG (all units in mm).

Photos are converted to vector halftone dots so they stay sharp at any print
size, even though the source images are low-res screenshots.
Run: python3 build.py && node render.js
"""
import math
import os
import re

import numpy as np
import qrcode
from PIL import Image, ImageFilter, ImageOps

HERE = os.path.dirname(os.path.abspath(__file__))
SRC = os.path.join(HERE, "..", "source-photos")

GREEN = "#1ED760"   # Spotify brand green
BLACK = "#0A0A0A"
WHITE = "#FFFFFF"
LINK = "https://linktr.ee/Zyonmusic"

SPOTIFY_PATH = re.search(r'd="([^"]+)"', open(os.path.join(HERE, "spotify.svg")).read()).group(1)


def load_lum(name, crop, blur=1.0, lo=2, hi=98, gamma=1.0):
    im = Image.open(os.path.join(SRC, name)).convert("L").crop(crop)
    im = im.filter(ImageFilter.GaussianBlur(blur))
    a = np.asarray(im).astype(float)
    p_lo, p_hi = np.percentile(a, lo), np.percentile(a, hi)
    a = np.clip((a - p_lo) / max(p_hi - p_lo, 1), 0, 1) ** gamma
    return a


def bilinear(a, x, y):
    h, w = a.shape
    x = min(max(x, 0), w - 1.001)
    y = min(max(y, 0), h - 1.001)
    x0, y0 = int(x), int(y)
    fx, fy = x - x0, y - y0
    return (a[y0, x0] * (1 - fx) * (1 - fy) + a[y0, x0 + 1] * fx * (1 - fy)
            + a[y0 + 1, x0] * (1 - fx) * fy + a[y0 + 1, x0 + 1] * fx * fy)


def halftone(lum, rect, cell, color, angle=45, invert=False, fade=None, max_r=0.62):
    """Vector halftone of `lum` stretched (cover) into rect=(x, y, w, h) in mm.

    Dot area is proportional to brightness (or darkness when invert=True).
    fade(u, v) -> 0..1 multiplies the tone, with u, v in 0..1 across the rect.
    """
    x0, y0, w, h = rect
    ih, iw = lum.shape
    sx, sy = iw / w, ih / h
    t = math.radians(angle)
    ct, st = math.cos(t), math.sin(t)
    cx, cy = x0 + w / 2, y0 + h / 2
    n = int(math.hypot(w, h) / cell / 2) + 2
    dots = []
    for i in range(-n, n + 1):
        for j in range(-n, n + 1):
            px = cx + (i * ct - j * st) * cell
            py = cy + (i * st + j * ct) * cell
            if not (x0 - cell < px < x0 + w + cell and y0 - cell < py < y0 + h + cell):
                continue
            u, v = (px - x0) / w, (py - y0) / h
            if not (0 <= u <= 1 and 0 <= v <= 1):
                continue
            val = bilinear(lum, (px - x0) * sx, (py - y0) * sy)
            if invert:
                val = 1 - val
            if fade:
                val *= fade(u, v)
            r = cell * max_r * math.sqrt(max(val, 0))
            if r < cell * 0.07:
                continue
            dots.append(f"M{px - r:.2f} {py:.2f}a{r:.2f} {r:.2f} 0 1 0 {2 * r:.2f} 0a{r:.2f} {r:.2f} 0 1 0 {-2 * r:.2f} 0")
    return f'<path fill="{color}" d="{"".join(dots)}"/>'


def qr(x, y, size, fg, bg=None, quiet=2):
    q = qrcode.QRCode(error_correction=qrcode.constants.ERROR_CORRECT_M, border=0)
    q.add_data(LINK)
    q.make(fit=True)
    m = q.get_matrix()
    n = len(m)
    s = size / (n + 2 * quiet)
    out = [f'<rect x="{x}" y="{y}" width="{size}" height="{size}" rx="{s * 1.5:.2f}" fill="{bg}"/>'] if bg else []
    d = "".join(f"M{x + (quiet + c) * s:.3f} {y + (quiet + r) * s:.3f}h{s + 0.02:.3f}v{s + 0.02:.3f}h{-s - 0.02:.3f}z"
                for r, row in enumerate(m) for c, on in enumerate(row) if on)
    out.append(f'<path fill="{fg}" d="{d}"/>')
    return "".join(out)


def spotify_icon(x, y, size, color):
    k = size / 24
    return f'<path fill="{color}" transform="translate({x} {y}) scale({k:.4f})" d="{SPOTIFY_PATH}"/>'


def text(x, y, s, size, color, font="Anton", anchor="start", fit=None, ls=0, extra=""):
    """fit=width in mm: the renderer rescales font-size so the text spans exactly that width."""
    f = f' data-fit="{fit}"' if fit else ""
    return (f'<text x="{x}" y="{y}" font-family="{font}" font-size="{size}" fill="{color}" '
            f'text-anchor="{anchor}" letter-spacing="{ls}"{f} {extra}>{s}</text>')


def page(name, trim_w, trim_h, bleed, body, guides):
    W, H = trim_w + 2 * bleed, trim_h + 2 * bleed
    html = f"""<!doctype html><html><head><meta charset="utf-8"><title>{name}</title><style>
@font-face{{font-family:Anton;src:url(fonts/anton-latin-400-normal.woff2)}}
@font-face{{font-family:Grotesk;font-weight:500;src:url(fonts/space-grotesk-latin-500-normal.woff2)}}
@font-face{{font-family:Grotesk;font-weight:700;src:url(fonts/space-grotesk-latin-700-normal.woff2)}}
@font-face{{font-family:Archivo;src:url(fonts/archivo-black-latin-400-normal.woff2)}}
@page{{size:{W}mm {H}mm;margin:0}}
html,body{{margin:0;padding:0;background:#000}}
svg{{display:block;width:{W}mm;height:{H}mm}}
body.guides #guides{{display:inline}} #guides{{display:none}}
</style></head><body>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="{-bleed} {-bleed} {W} {H}" data-w="{W}" data-h="{H}">
{body}
<g id="guides" fill="none" stroke-width="1.5">
<rect x="0" y="0" width="{trim_w}" height="{trim_h}" stroke="#ff00ff"/>
{guides}
</g>
</svg></body></html>"""
    with open(os.path.join(HERE, f"{name}.html"), "w") as fh:
        fh.write(html)


def smooth(e0, e1, t):
    t = min(max((t - e0) / (e1 - e0), 0), 1)
    return t * t * (3 - 2 * t)


# ---------------------------------------------------------------------------
# A) Roll-up / pull-up banner 850 x 2000 mm, photo 1 (cut-out on black)
# ---------------------------------------------------------------------------
def rollup():
    W, H, B = 850, 2000, 10
    lum = load_lum("1_cutout.png", (262, 130, 882, 884), blur=0.8, lo=1, hi=99.5, gamma=1.15)
    photo_rect = (-B, 170, W + 2 * B, 1058)
    ht = halftone(lum, photo_rect, 6.2, GREEN, angle=45,
                  fade=lambda u, v: 1 - smooth(0.78, 0.99, v))
    body = f"""
<rect x="{-B}" y="{-B}" width="{W + 2 * B}" height="{H + 2 * B}" fill="{BLACK}"/>
{ht}
<!-- header -->
{spotify_icon(60, 62, 64, GREEN)}
{text(140, 113, "LISTEN ON SPOTIFY", 44, WHITE, "Grotesk", extra='font-weight="700"', ls=1.5)}
<rect x="60" y="158" width="730" height="3" fill="{GREEN}"/>
<!-- name -->
{text(W / 2, 1530, "ZYON", 400, WHITE, "Anton", anchor="middle", fit=730, ls=4)}
<rect x="60" y="1566" width="730" height="3" fill="{GREEN}"/>
{text(60, 1620, "NEW MUSIC · STREAMING NOW", 40, GREEN, "Grotesk", extra='font-weight="700"', fit=730)}
<!-- CTA -->
{qr(60, 1655, 190, BLACK, WHITE)}
{text(275, 1700, "SCAN TO STREAM", 50, WHITE, "Anton", ls=1)}
{text(275, 1748, "linktr.ee/Zyonmusic", 33, WHITE, "Grotesk", extra='font-weight="700"')}
{text(275, 1793, "@zyon_333", 33, GREEN, "Grotesk", extra='font-weight="700"')}
{text(790, 1838, "PHOTO: @FRAMED.BY.ICARUS", 13, "#8a8a8a", "Grotesk", anchor="end", extra='font-weight="500"', ls=1)}
"""
    guides = f'<rect x="40" y="40" width="{W - 80}" height="{H - 40 - 150}" stroke="#00ffff" stroke-dasharray="10 6"/>'
    page("A_rollup_850x2000mm", W, H, B, body, guides)


# ---------------------------------------------------------------------------
# B) PVC banner 6ft x 3ft (1830 x 915 mm), photo 4 close-up, green field
# ---------------------------------------------------------------------------
def pvc():
    W, H, B = 1830, 915, 10
    lum = load_lum("4_closeup.png", (150, 0, 1110, 884), blur=0.9, lo=1, hi=99, gamma=1.1)
    photo_rect = (-B, -B, 1040, H + 2 * B)
    ht = halftone(lum, photo_rect, 7.5, BLACK, angle=45, invert=True,
                  fade=lambda u, v: 1 - smooth(0.80, 1.0, u))
    rx = 1060
    body = f"""
<rect x="{-B}" y="{-B}" width="{W + 2 * B}" height="{H + 2 * B}" fill="{GREEN}"/>
{ht}
{text(rx, 470, "ZYON", 400, BLACK, "Anton", fit=710, ls=4)}
<rect x="{rx}" y="505" width="710" height="5" fill="{BLACK}"/>
{spotify_icon(rx, 540, 74, BLACK)}
{text(rx + 95, 598, "LISTEN ON SPOTIFY", 62, BLACK, "Archivo", fit=615)}
{qr(rx - 8, 647, 221, BLACK, None, quiet=2)}
{text(rx + 235, 710, "SCAN TO STREAM", 62, BLACK, "Anton", ls=1)}
{text(rx + 235, 770, "linktr.ee/Zyonmusic", 40, BLACK, "Grotesk", extra='font-weight="700"')}
{text(rx + 235, 822, "@zyon_333", 40, BLACK, "Grotesk", extra='font-weight="700"')}
{text(60, 860, "PHOTO: @FRAMED.BY.ICARUS", 14, BLACK, "Grotesk", extra='font-weight="500" opacity="0.7"', ls=1)}
"""
    guides = f'<rect x="50" y="50" width="{W - 100}" height="{H - 100}" stroke="#ff0000" stroke-dasharray="10 6"/>'
    page("B_pvc_banner_1830x915mm", W, H, B, body, guides)


# ---------------------------------------------------------------------------
# C) A1 poster 594 x 841 mm, photo 3 on stage, angled green band
# ---------------------------------------------------------------------------
def poster():
    W, H, B = 594, 841, 3
    lum = load_lum("3_stage_bw.png", (90, 0, 714, 884), blur=0.7, lo=2, hi=99.5, gamma=1.2)
    photo_rect = (-B, -B, W + 2 * B, 640)
    ht = halftone(lum, photo_rect, 4.2, WHITE, angle=45,
                  fade=lambda u, v: 1 - smooth(0.80, 1.0, v))
    body = f"""
<rect x="{-B}" y="{-B}" width="{W + 2 * B}" height="{H + 2 * B}" fill="{BLACK}"/>
{ht}
<g transform="rotate(-7 {W / 2} 600)">
  <rect x="-80" y="400" width="{W + 160}" height="280" fill="{GREEN}"/>
  {text(W / 2, 638, "ZYON", 300, BLACK, "Anton", anchor="middle", fit=430, ls=3)}
</g>
{spotify_icon(40, 735, 40, GREEN)}
{text(92, 770, "LISTEN ON SPOTIFY", 36, WHITE, "Archivo", fit=330)}
{text(40, 800, "linktr.ee/Zyonmusic  ·  @zyon_333", 17, "#bdbdbd", "Grotesk", extra='font-weight="700"')}
{qr(440, 690, 120, BLACK, WHITE)}
{text(500, 826, "SCAN TO STREAM", 15, WHITE, "Grotesk", anchor="middle", extra='font-weight="700"', ls=1)}
{text(40, 822, "PHOTO: @FRAMED.BY.ICARUS", 8, "#8a8a8a", "Grotesk", extra='font-weight="500"', ls=0.8)}
"""
    guides = f'<rect x="15" y="15" width="{W - 30}" height="{H - 30}" stroke="#00ffff" stroke-dasharray="6 4"/>'
    page("C_poster_A1_594x841mm", W, H, B, body, guides)


if __name__ == "__main__":
    rollup()
    pvc()
    poster()
    print("built")
