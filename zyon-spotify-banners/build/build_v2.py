"""v2 designs: AI-upscaled photos (prep_photos.py), subject-over-type layering,
grain/riso texture and a recurring 'now playing 1:11 / 3:33' motif (nod to zyon_333).
Run: python3 build_v2.py && node render.js v2_
"""
import os

import numpy as np
from PIL import Image, ImageFilter, ImageOps

from build import GREEN, BLACK, WHITE, halftone, load_lum, qr, smooth, spotify_icon

HERE = os.path.dirname(os.path.abspath(__file__))
HIRES = os.path.join(HERE, "..", "source-photos", "hires")
IMG = os.path.join(HERE, "img")
os.makedirs(IMG, exist_ok=True)

MINT = "#D9FFE6"
GREY = "#B3B3B3"
PAPER = "#EDEAE2"
CAP = {"Anton": 0.859, "Grotesk": 0.70, "Archivo": 0.688, "Mono": 0.70}
RNG = np.random.default_rng(333)


# --- raster helpers ---------------------------------------------------------
def hex_rgb(h):
    return np.array([int(h[i:i + 2], 16) for i in (1, 3, 5)], float) / 255


def load(name, choke=0):
    """choke>0 erodes the mask (px at x4 scale) to drop light fringes from the old background."""
    im = Image.open(os.path.join(HIRES, name + "_x4.jpg")).convert("RGB")
    m = Image.open(os.path.join(HIRES, name + "_mask.png")).convert("L")
    if choke:
        m = m.filter(ImageFilter.MinFilter(2 * choke + 1)).filter(ImageFilter.GaussianBlur(choke * 0.6))
    return im, m


def place(img, scale, off, rect, dpi):
    """Resample img (x4 upscale) for rect=(x, y, w, h) mm, where an ORIGINAL-photo
    pixel p lands at off + p * scale (mm). Out-of-frame areas come back black."""
    x, y, w, h = rect
    s = scale / 4
    box = ((x - off[0]) / s, (y - off[1]) / s, (x + w - off[0]) / s, (y + h - off[1]) / s)
    size = (round(w * dpi / 25.4), round(h * dpi / 25.4))
    ib = tuple(int(np.floor(v)) if i < 2 else int(np.ceil(v)) for i, v in enumerate(box))
    padded = img.crop(ib)  # PIL pads out-of-bounds with black
    return padded.resize(size, Image.LANCZOS, box=(box[0] - ib[0], box[1] - ib[1], box[2] - ib[0], box[3] - ib[1]))


def lum(img, mask=None, lo=1, hi=99.5, gamma=1.0, s_curve=0.0):
    a = np.asarray(img).astype(float) / 255
    L = a[..., 0] * 0.3 + a[..., 1] * 0.59 + a[..., 2] * 0.11
    ref = L[mask > 0.5] if mask is not None and (mask > 0.5).any() else L
    p0, p1 = np.percentile(ref, lo), np.percentile(ref, hi)
    L = np.clip((L - p0) / max(p1 - p0, 1e-3), 0, 1) ** gamma
    if s_curve:
        L = L * (1 - s_curve) + (0.5 - 0.5 * np.cos(np.pi * L)) * s_curve
    return L


def grain(L, amount=0.06):
    return np.clip(L + RNG.normal(0, amount, L.shape), 0, 1)


def tone_map(L, stops):
    xs = [p for p, _ in stops]
    cols = np.array([hex_rgb(c) for _, c in stops])
    return np.stack([np.interp(L, xs, cols[:, i]) for i in range(3)], -1)


def nsmooth(e0, e1, x):
    x = np.clip((x - e0) / (e1 - e0), 0, 1)
    return x * x * (3 - 2 * x)


def fade_mask(shape, fx=None, fy=None):
    h, w = shape
    u, v = np.meshgrid(np.linspace(0, 1, w), np.linspace(0, 1, h))
    m = np.ones(shape)
    if fx:
        m *= fx(u)
    if fy:
        m *= fy(v)
    return m


def save(rgb, name, alpha=None):
    a = (np.clip(rgb, 0, 1) * 255).round().astype(np.uint8)
    if alpha is None:
        Image.fromarray(a).save(os.path.join(IMG, name + ".jpg"), quality=92)
        return f"img/{name}.jpg"
    al = (np.clip(alpha, 0, 1) * 255).round().astype(np.uint8)
    Image.fromarray(np.dstack([a, al]), "RGBA").save(os.path.join(IMG, name + ".png"), optimize=True)
    return f"img/{name}.png"


def shadow(alpha, name, blur_px=14, strength=0.55):
    """Soft contact shadow from a subject alpha, so the subject sits in front of the type."""
    a = Image.fromarray((np.clip(alpha, 0, 1) * 255).astype(np.uint8)).reduce(4).filter(ImageFilter.GaussianBlur(blur_px))
    al = np.asarray(a).astype(float) / 255 * strength
    return save(np.zeros(al.shape + (3,)), name, al)


def image(href, rect, extra=""):
    x, y, w, h = rect
    return f'<image href="{href}" x="{x}" y="{y}" width="{w}" height="{h}" preserveAspectRatio="none" {extra}/>'


def grain_tile(name, dark_only=False, strength=60):
    n = RNG.normal(0, 1, (600, 600))
    rgb = np.zeros((600, 600, 3)) if dark_only else np.repeat((n > 0)[..., None], 3, -1).astype(float)
    alpha = np.clip(np.abs(n) * strength, 0, 255) / 255 * (n < 0 if dark_only else 1)
    return save(rgb, name, alpha)


# --- vector helpers ---------------------------------------------------------
def t(x, y, s, size, color, font="Grotesk", weight=700, anchor="start", ls=0, fit=None, captop=None, extra=""):
    """y is the baseline, or pass captop= to have the renderer put the cap-height top there."""
    attrs = f' data-fit="{fit}"' if fit else ""
    if captop is not None:
        attrs += f' data-captop="{captop}" data-cap="{CAP[font]}"'
        y = captop + size * CAP[font]
    return (f'<text x="{x}" y="{y:.2f}" font-family="{font}" font-weight="{weight}" font-size="{size}" '
            f'fill="{color}" text-anchor="{anchor}" letter-spacing="{ls}"{attrs} {extra}>{s}</text>')


def progress(x, y, w, color=WHITE, rest="#4D4D4D", h=5, knob=8, times_y=None, tcolor=GREY, tsize=18, at=1 / 3):
    """Spotify-style scrubber at 1:11 of 3:33."""
    k = x + w * at
    out = (f'<rect x="{x}" y="{y - h / 2}" width="{w}" height="{h}" rx="{h / 2}" fill="{rest}"/>'
           f'<rect x="{x}" y="{y - h / 2}" width="{k - x}" height="{h}" rx="{h / 2}" fill="{color}"/>'
           f'<circle cx="{k}" cy="{y}" r="{knob}" fill="{color}"/>')
    if times_y:
        out += t(x, times_y, "1:11", tsize, tcolor, "Mono", 400) + t(x + w, times_y, "3:33", tsize, tcolor, "Mono", 400, "end")
    return out


def controls(cx, cy, r, color=WHITE, play=GREEN, gap=None):
    gap = gap or r * 3.1
    s = r * 0.55

    def skip(x, d):
        tri = f'M{x - d * s * 0.6} {cy - s}L{x + d * s * 0.6} {cy}L{x - d * s * 0.6} {cy + s}Z'
        return f'<path fill="{color}" d="{tri}"/><rect x="{x + d * s * 0.6 - (s * 0.22 if d > 0 else 0)}" y="{cy - s}" width="{s * 0.22}" height="{2 * s}" fill="{color}"/>'
    tri = f'M{cx - r * 0.3} {cy - r * 0.42}L{cx + r * 0.45} {cy}L{cx - r * 0.3} {cy + r * 0.42}Z'
    return (skip(cx - gap, -1) + skip(cx + gap, 1)
            + f'<circle cx="{cx}" cy="{cy}" r="{r}" fill="{play}"/><path fill="{BLACK}" d="{tri}"/>')


def page(name, trim_w, trim_h, bleed, body, guides=""):
    W, H = trim_w + 2 * bleed, trim_h + 2 * bleed
    html = f"""<!doctype html><html><head><meta charset="utf-8"><title>{name}</title><style>
@font-face{{font-family:Anton;src:url(fonts/anton-latin-400-normal.woff2)}}
@font-face{{font-family:Grotesk;font-weight:500;src:url(fonts/space-grotesk-latin-500-normal.woff2)}}
@font-face{{font-family:Grotesk;font-weight:700;src:url(fonts/space-grotesk-latin-700-normal.woff2)}}
@font-face{{font-family:Archivo;src:url(fonts/archivo-black-latin-400-normal.woff2)}}
@font-face{{font-family:Mono;font-weight:400;src:url(fonts/space-mono-latin-400-normal.woff2)}}
@font-face{{font-family:Mono;font-weight:700;src:url(fonts/space-mono-latin-700-normal.woff2)}}
@page{{size:{W}mm {H}mm;margin:0}}
html,body{{margin:0;padding:0;background:#000}}
svg{{display:block;width:{W}mm;height:{H}mm}}
body.guides #guides{{display:inline}} #guides{{display:none}}
</style></head><body>
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="{-bleed} {-bleed} {W} {H}" data-w="{W}" data-h="{H}">
{body}
<g id="guides" fill="none" stroke-width="1.5"><rect x="0" y="0" width="{trim_w}" height="{trim_h}" stroke="#ff00ff"/>{guides}</g>
</svg></body></html>"""
    with open(os.path.join(HERE, f"{name}.html"), "w") as fh:
        fh.write(html)


def grain_layer(href, B, W, H, tile=100, opacity=1.0, blend=None):
    style = f' style="mix-blend-mode:{blend}"' if blend else ""
    return (f'<defs><pattern id="g{tile}" width="{tile}" height="{tile}" patternUnits="userSpaceOnUse">'
            f'<image href="{href}" width="{tile}" height="{tile}"/></pattern></defs>'
            f'<rect x="{-B}" y="{-B}" width="{W + 2 * B}" height="{H + 2 * B}" fill="url(#g{tile})" opacity="{opacity}"{style}/>')


GRAIN = None


def grain_href():
    global GRAIN
    GRAIN = GRAIN or grain_tile("grain_light", strength=45)
    return GRAIN


# ---------------------------------------------------------------------------
# A) Roll-up 850 x 2000: magazine-cover layering, head in front of ZYON
# ---------------------------------------------------------------------------
def rollup():
    W, H, B, dpi = 850, 2000, 10, 85
    im, m = load("1_cutout")
    scale = 1.78                     # mm per original px
    off = (425 - 565 * scale, 390 - 150 * scale)   # face centre ~x565, head top ~y150 (orig px)
    rect = (-B, 300, W + 2 * B, 1420)
    L = lum(place(im, scale, off, rect, dpi), lo=1, hi=99.7, gamma=1.05, s_curve=0.35)
    a = np.asarray(place(m, scale, off, rect, dpi)).astype(float) / 255
    a *= fade_mask(a.shape, fy=lambda v: 1 - nsmooth(0.72, 0.97, v))
    rgb = tone_map(grain(L, 0.05), [(0, BLACK), (0.08, "#06140B"), (0.62, GREEN), (1, MINT)])
    fig = save(rgb, "A_figure", a)
    shd = shadow(a, "A_shadow")

    body = f"""
<rect x="{-B}" y="{-B}" width="{W + 2 * B}" height="{H + 2 * B}" fill="{BLACK}"/>
{spotify_icon(60, 66, 44, GREEN)}
{t(118, 101, "LISTEN ON SPOTIFY", 32, WHITE, ls=1)}
{t(790, 101, "NEW MUSIC", 24, GREEN, "Mono", anchor="end", ls=2)}
{t(W / 2, 0, "ZYON", 400, WHITE, "Anton", 400, "middle", ls=2, fit=770, captop=175)}
{image(shd, (rect[0] + 6, rect[1] + 10, rect[2], rect[3]))}
{image(fig, rect)}
{grain_layer(grain_href(), B, W, H, opacity=0.55)}
{t(790, 1650, "PHOTO @FRAMED.BY.ICARUS", 12, "#8a8a8a", weight=500, anchor="end", ls=1)}
{t(60, 1694, "SCAN TO STREAM →", 22, GREEN, "Mono", ls=1)}
{progress(60, 1722, 510, times_y=1756, tsize=19)}
{t(60, 1806, "ZYON", 54, WHITE)}
{t(60, 1842, "@zyon_333 · linktr.ee/Zyonmusic", 22, GREY, weight=500)}
{qr(610, 1670, 180, BLACK, WHITE)}
"""
    guides = f'<rect x="40" y="40" width="{W - 80}" height="{H - 190}" stroke="#00ffff" stroke-dasharray="10 6"/>'
    page("v2_A_rollup_850x2000mm", W, H, B, body, guides)


# ---------------------------------------------------------------------------
# B) PVC 1830 x 915: billboard, duotone close-up, N tucked behind his cap
# ---------------------------------------------------------------------------
def pvc():
    W, H, B, dpi = 1830, 915, 10, 75
    im, m = load("4_closeup")
    scale = 1.10
    off = (1450 - 620 * scale, -30)
    rect = (790, -B, W + B - 790, H + 2 * B)
    src = place(im, scale, off, rect, dpi)
    a_subj = np.asarray(place(m, scale, off, rect, dpi)).astype(float) / 255
    L = grain(lum(src, lo=1, hi=99.6, gamma=1.1, s_curve=0.4), 0.045)
    rgb = tone_map(L, [(0, BLACK), (0.1, "#04120A"), (0.66, GREEN), (1, MINT)])
    left = fade_mask(L.shape, fy=None, fx=lambda u: nsmooth(0.0, 0.28, u))
    bg = save(rgb * left[..., None] + hex_rgb(BLACK) * (1 - left[..., None]), "B_photo")
    subj = save(rgb, "B_subject", a_subj * left)
    shd = shadow(a_subj * left, "B_shadow", blur_px=10)

    body = f"""
<rect x="{-B}" y="{-B}" width="{W + 2 * B}" height="{H + 2 * B}" fill="{BLACK}"/>
{image(bg, rect)}
{t(50, 0, "ZYON", 500, WHITE, "Anton", 400, ls=3, fit=1090, captop=110)}
{image(shd, (rect[0] - 8, rect[1] + 8, rect[2], rect[3]))}
{image(subj, rect)}
{grain_layer(grain_href(), B, W, H, opacity=0.5)}
{t(52, 88, "NOW PLAYING", 24, GREEN, "Mono", ls=3)}
{qr(50, 672, 188, BLACK, WHITE)}
{t(270, 703, "SCAN TO STREAM", 24, GREEN, "Mono", ls=2)}
{spotify_icon(270, 722, 50, GREEN)}
{t(334, 763, "LISTEN ON SPOTIFY", 52, WHITE, "Archivo", 400)}
{progress(272, 800, 600, times_y=832, tsize=19)}
{t(272, 862, "linktr.ee/Zyonmusic  ·  @zyon_333", 26, GREY, weight=500)}
{t(1780, 862, "PHOTO @FRAMED.BY.ICARUS", 13, WHITE, weight=500, anchor="end", ls=1, extra='opacity="0.75"')}
"""
    guides = f'<rect x="50" y="50" width="{W - 100}" height="{H - 100}" stroke="#ff0000" stroke-dasharray="10 6"/>'
    page("v2_B_pvc_banner_1830x915mm", W, H, B, body, guides)


# ---------------------------------------------------------------------------
# C) A1 "Now Playing": the poster is a Spotify player, the art is a cover
# ---------------------------------------------------------------------------
def now_playing():
    W, H, B = 594, 841, 3
    im, m = load("3_stage_bw", choke=5)
    # blurred art-colour background, Spotify-style gradient to black
    bgr = (-B, -B, W + 2 * B, H + 2 * B)
    small = place(im, 0.9, (-30, -40), bgr, 30).filter(ImageFilter.GaussianBlur(18))
    L = lum(small, lo=1, hi=99)
    v = np.linspace(0, 1, L.shape[0])[:, None] * np.ones(L.shape)
    base = tone_map(L * 0.5 + 0.25, [(0, "#06180E"), (1, "#2E8C55")])
    k = (1 - nsmooth(0.05, 0.62, v))[..., None]
    bg = save(base * k + hex_rgb("#0B0B0B") * (1 - k), "C_bg")

    art = (57, 35, 480, 480)
    scale = 0.62
    off = (262 - 306 * scale, 190 - 75 * scale)
    src = place(im, scale, off, art, 150)
    a = np.asarray(place(m, scale, off, art, 150)).astype(float) / 255
    Lf = grain(lum(src, a, lo=2, hi=99.5, gamma=1.15, s_curve=0.5), 0.05)
    subj = save(tone_map(Lf, [(0, BLACK), (1, WHITE)]), "C_subject", a)

    body = f"""
{image(bg, bgr)}
<defs><clipPath id="art"><rect x="{art[0]}" y="{art[1]}" width="{art[2]}" height="{art[3]}" rx="5"/></clipPath></defs>
<g clip-path="url(#art)">
  <rect x="{art[0]}" y="{art[1]}" width="{art[2]}" height="{art[3]}" fill="{GREEN}"/>
  {t(W / 2, 0, "ZYON", 300, BLACK, "Anton", 400, "middle", ls=2, fit=440, captop=58)}
  {image(subj, art)}
  {t(art[0] + 14, art[1] + art[3] - 14, "333", 26, BLACK, "Anton", 400)}
</g>
{grain_layer(grain_href(), B, W, H, tile=60, opacity=0.35)}
{t(537, 27, "PHOTO @FRAMED.BY.ICARUS", 7, GREY, weight=500, anchor="end", ls=0.6)}
{t(57, 562, "ZYON", 40, WHITE)}
{t(57, 590, "@zyon_333  ·  linktr.ee/Zyonmusic", 18, GREY, weight=500)}
{spotify_icon(503, 537, 34, GREEN)}
{progress(57, 614, 480, h=4, knob=6.5, times_y=638, tsize=13)}
{controls(W / 2, 678, 28)}
<rect x="57" y="722" width="480" height="0.8" fill="#3a3a3a"/>
{t(57, 0, "SCAN TO PLAY", 60, WHITE, "Anton", 400, fit=330, captop=736)}
{t(57, 818, "LISTEN ON SPOTIFY", 21, GREEN, "Archivo", 400, ls=0.5)}
{qr(437, 732, 100, BLACK, WHITE)}
"""
    guides = f'<rect x="15" y="15" width="{W - 30}" height="{H - 30}" stroke="#00ffff" stroke-dasharray="6 4"/>'
    page("v2_C_poster_A1_now_playing", W, H, B, body, guides)


# ---------------------------------------------------------------------------
# D) A1 riso: two-colour overprint (black + Spotify green) on paper
# ---------------------------------------------------------------------------
def riso():
    W, H, B = 594, 841, 3
    lumL = load_lum("2_crowd.png", (0, 0, 1179, 884), blur=0.6, lo=1, hi=99.5, gamma=0.8)
    eq = np.asarray(ImageOps.equalize(Image.fromarray((lumL * 255).astype(np.uint8)))).astype(float) / 255
    lumL = lumL * 0.45 + eq * 0.55
    rect = (-B, -B, W + 2 * B, 452)
    fade = lambda u, v: 1 - smooth(0.82, 1.0, v)
    blk = halftone(1 - (1 - lumL) ** 1.9, rect, 2.7, BLACK, angle=45, invert=True, fade=fade, max_r=0.6)
    grn = halftone(1 - (1 - lumL) ** 0.85, rect, 3.4, GREEN, angle=15, invert=True, fade=fade, max_r=0.62)
    speck = grain_tile("grain_paper", dark_only=True, strength=30)

    body = f"""
<rect x="{-B}" y="{-B}" width="{W + 2 * B}" height="{H + 2 * B}" fill="{PAPER}"/>
<g style="mix-blend-mode:multiply" transform="translate(2.2 1.6)">{grn}</g>
<g style="mix-blend-mode:multiply">{blk}</g>
<g style="mix-blend-mode:multiply">{t(W / 2, 0, "ZYON", 300, GREEN, "Anton", 400, "middle", ls=2, fit=556, captop=372)}</g>
{t(W / 2 + 4, 0, "ZYON", 300, "none", "Anton", 400, "middle", ls=2, fit=556, captop=376, extra=f'stroke="{BLACK}" stroke-width="1.6"')}
{t(574, 668, "PHOTO @FRAMED.BY.ICARUS", 7, BLACK, weight=500, anchor="end", ls=0.6)}
<rect x="20" y="676" width="554" height="1.6" fill="{BLACK}"/>
{t(20, 702, "NEW MUSIC — STREAMING NOW", 15, BLACK, "Mono", ls=1)}
{spotify_icon(20, 716, 34, BLACK)}
{t(62, 744, "LISTEN ON SPOTIFY", 30, BLACK, "Archivo", 400)}
{progress(21, 772, 380, color=BLACK, rest="#BDB9AE", h=3.4, knob=5.5, times_y=792, tcolor=BLACK, tsize=12)}
{t(20, 820, "linktr.ee/Zyonmusic  ·  @zyon_333", 15, BLACK)}
{qr(460, 694, 114, BLACK, None, quiet=1)}
{t(517, 822, "SCAN TO STREAM", 11, BLACK, "Mono", anchor="middle", ls=1)}
{grain_layer(speck, B, W, H, tile=80, opacity=1, blend="multiply")}
"""
    guides = f'<rect x="15" y="15" width="{W - 30}" height="{H - 30}" stroke="#00ffff" stroke-dasharray="6 4"/>'
    page("v2_D_poster_A1_riso", W, H, B, body, guides)


if __name__ == "__main__":
    import sys
    todo = {"A": rollup, "B": pvc, "C": now_playing, "D": riso}
    for k in (sys.argv[1:] or todo):
        todo[k]()
        print("built", k)
