"""v3: the v2 concepts pushed for stopping power: big flat Spotify-green fields, black &
white subjects with a sticker-cut border, starburst stickers, sound-wave rings, echo type.
Run: python3 build_v3.py && node render.js v3_
"""
import math

import numpy as np
from PIL import Image, ImageFilter

from build import GREEN, BLACK, WHITE, halftone, qr, smooth, spotify_icon
from build_v2 import (GREY, PAPER, controls, fade_mask, grain, grain_href, grain_layer, grain_tile,
                      image, load, lum, nsmooth, page, place, progress, save, shadow, t, tone_map)


# --- v3 helpers -------------------------------------------------------------
def bw(src, a, s_curve=0.55, gamma=1.1, g=0.045):
    return tone_map(grain(lum(src, a, lo=1.5, hi=99.5, gamma=gamma, s_curve=s_curve), g),
                    [(0, BLACK), (1, WHITE)])


def sticker_alpha(a, border_px):
    """Outer sticker-cut outline: dilate the subject alpha by ~border_px with a clean round edge."""
    im = Image.fromarray((np.clip(a, 0, 1) * 255).astype(np.uint8))
    im = im.filter(ImageFilter.GaussianBlur(border_px * 0.55))
    b = np.asarray(im).astype(float) / 255
    b = np.clip((b - 0.06) / 0.08, 0, 1)
    return np.maximum(b, a)


def subject_layers(prefix, rgb, a, rect, border_px, shadow_off=(7, 9), shadow_strength=0.5):
    """Returns SVG for shadow + white sticker border + subject, drawn in that order."""
    border = sticker_alpha(a, border_px)
    s = save(np.ones(rgb.shape), prefix + "_border", border)
    shd = shadow(border, prefix + "_shadow", blur_px=8, strength=shadow_strength)
    subj = save(rgb, prefix + "_subject", a)
    x, y, w, h = rect
    return (image(shd, (x + shadow_off[0], y + shadow_off[1], w, h))
            + image(s, rect) + image(subj, rect))


def starburst(cx, cy, r, rot, fill, ink, lines, n=22, inner=0.84, stroke=None):
    pts = []
    for i in range(2 * n):
        rr = r if i % 2 == 0 else r * inner
        ang = math.pi * i / n
        pts.append(f"{cx + rr * math.cos(ang):.2f},{cy + rr * math.sin(ang):.2f}")
    st = f' stroke="{stroke}" stroke-width="{r * 0.03:.2f}"' if stroke else ""
    out = [f'<g transform="rotate({rot} {cx} {cy})"><polygon points="{" ".join(pts)}" fill="{fill}"{st}/>']
    # lines: [(text, cap_height_mm)], stacked and centred
    gap = r * 0.08
    total = sum(c for _, c in lines) + gap * (len(lines) - 1)
    y = cy - total / 2
    for s, c in lines:
        size = c / 0.859
        out.append(t(cx, y + c, s, size, ink, "Anton", 400, "middle", ls=c * 0.03))
        y += c + gap
    out.append("</g>")
    return "".join(out)


def rings(cx, cy, radii, width, color, opacity):
    return "".join(f'<circle cx="{cx}" cy="{cy}" r="{r}" fill="none" stroke="{color}" '
                   f'stroke-width="{width}" opacity="{opacity}"/>' for r in radii)


def eq_bars(x, y, h, w, color, heights=(0.55, 1.0, 0.35, 0.8)):
    """Spotify 'playing' equaliser bars, bottoms aligned on y."""
    gap = w * 0.6
    return "".join(f'<rect x="{x + i * (w + gap):.2f}" y="{y - h * k:.2f}" width="{w}" height="{h * k:.2f}" rx="{w * 0.25}" fill="{color}"/>'
                   for i, k in enumerate(heights))


def outline(x, captop, s, fit, color, width, anchor="middle", opacity=1):
    return t(x, 0, s, 300, "none", "Anton", 400, anchor, ls=2, fit=fit, captop=captop,
             extra=f'stroke="{color}" stroke-width="{width}" opacity="{opacity}"')


# ---------------------------------------------------------------------------
# A) Roll-up 850 x 2000 - green field, head over ZYON, echo type
# ---------------------------------------------------------------------------
def rollup():
    W, H, B, dpi = 850, 2000, 10, 80
    im, m = load("1_cutout", choke=2)
    scale = 1.62
    off = (425 - 565 * scale, 372 - 141 * scale)     # face centre x565, cap top y141 (orig px, from mask)
    rect = (-B, 320, W + 2 * B, 1180)
    src = place(im, scale, off, rect, dpi)
    a = np.asarray(place(m, scale, off, rect, dpi)).astype(float) / 255
    subject = subject_layers("v3A", bw(src, a), a, rect, border_px=30)

    body = f"""
<rect x="{-B}" y="{-B}" width="{W + 2 * B}" height="{H + 2 * B}" fill="{GREEN}"/>
{t(60, 100, "NOW PLAYING", 26, BLACK, "Mono", ls=3)}
{spotify_icon(746, 66, 44, BLACK)}
{t(W / 2, 0, "ZYON", 400, BLACK, "Anton", 400, "middle", ls=2, fit=790, captop=140)}
{outline(W / 2, 535, "ZYON", 790, BLACK, 3.2)}
{outline(W / 2, 930, "ZYON", 790, BLACK, 3.2, opacity=0.55)}
{subject}
{starburst(705, 1215, 112, -12, WHITE, BLACK, [("NEW", 62), ("MUSIC", 40)], stroke=BLACK)}
{grain_layer(grain_href(), B, W, H, opacity=0.5)}
<rect x="{-B}" y="1455" width="{W + 2 * B}" height="{H - 1455 + B}" fill="{BLACK}"/>
<rect x="{-B}" y="1455" width="{W + 2 * B}" height="8" fill="{WHITE}"/>
{t(790, 1492, "PHOTO @FRAMED.BY.ICARUS", 12, "#8a8a8a", weight=500, anchor="end", ls=1)}
{spotify_icon(60, 1512, 46, GREEN)}
{t(124, 0, "LISTEN ON SPOTIFY", 50, WHITE, "Archivo", 400, fit=666, captop=1520)}
{progress(60, 1606, 730, times_y=1640, tsize=20)}
{t(60, 0, "SCAN TO STREAM", 80, WHITE, "Anton", 400, fit=520, captop=1672)}
{t(60, 1796, "linktr.ee/Zyonmusic", 30, WHITE)}
{t(60, 1838, "@zyon_333", 30, GREEN)}
{qr(615, 1668, 175, BLACK, WHITE)}
"""
    guides = f'<rect x="40" y="40" width="{W - 80}" height="{H - 190}" stroke="#00ffff" stroke-dasharray="10 6"/>'
    page("v3_A_rollup_850x2000mm", W, H, B, body, guides)


# ---------------------------------------------------------------------------
# B) PVC 1830 x 915 - green field, sound-wave rings off the mic, sticker cut-out
# ---------------------------------------------------------------------------
def pvc():
    W, H, B, dpi = 1830, 915, 10, 75
    im, m = load("4_closeup", choke=3)
    scale = 1.10
    off = (1480 - 620 * scale, -30)
    rect = (700, -B, W + B - 700, H + 2 * B)
    src = place(im, scale, off, rect, dpi)
    a = np.asarray(place(m, scale, off, rect, dpi)).astype(float) / 255
    mic = (off[0] + 640 * scale, off[1] + 520 * scale)
    # trim the shoulder (cut by the photo's left edge) along an arc around the mic, echoing the rings
    h, w = a.shape
    xs = rect[0] + (np.arange(w) + 0.5) * rect[2] / w
    ys = rect[1] + (np.arange(h) + 0.5) * rect[3] / h
    d = np.hypot(xs[None, :] - mic[0], ys[:, None] - mic[1])
    a *= 1 - nsmooth(612, 618, d)
    subject = subject_layers("v3B", bw(src, a, s_curve=0.6), a, rect, border_px=26, shadow_off=(-9, 9))

    body = f"""
<rect x="{-B}" y="{-B}" width="{W + 2 * B}" height="{H + 2 * B}" fill="{GREEN}"/>
{rings(*mic, [150, 265, 390, 525, 670, 825, 990, 1165, 1350], 28, BLACK, 0.12)}
{t(52, 84, "NOW PLAYING", 24, BLACK, "Mono", ls=3)}
{t(50, 0, "ZYON", 500, BLACK, "Anton", 400, ls=3, fit=1110, captop=105)}
{subject}
{starburst(1695, 185, 112, 12, WHITE, BLACK, [("NEW", 62), ("MUSIC", 40)], stroke=BLACK)}
{grain_layer(grain_href(), B, W, H, opacity=0.45)}
{qr(50, 655, 210, BLACK, WHITE)}
{t(290, 688, "SCAN TO STREAM", 24, BLACK, "Mono", ls=2)}
{spotify_icon(290, 710, 54, BLACK)}
{t(360, 0, "LISTEN ON SPOTIFY", 50, BLACK, "Archivo", 400, fit=400, captop=724)}
{progress(292, 800, 468, color=BLACK, rest="#0d6b33", times_y=832, tcolor=BLACK, tsize=19)}
{t(292, 862, "linktr.ee/Zyonmusic  ·  @zyon_333", 25, BLACK)}
{t(52, 628, "PHOTO @FRAMED.BY.ICARUS", 13, BLACK, weight=500, ls=1, extra='opacity="0.7"')}
"""
    guides = f'<rect x="50" y="50" width="{W - 100}" height="{H - 100}" stroke="#00ffff" stroke-dasharray="10 6"/>'
    page("v3_B_pvc_banner_1830x915mm", W, H, B, body, guides)


# ---------------------------------------------------------------------------
# C) A1 Now Playing - neon glow, sticker cut-out on the cover, NEW sticker, EQ bars
# ---------------------------------------------------------------------------
def now_playing():
    W, H, B = 594, 841, 3
    im, m = load("3_stage_bw", choke=5)
    art = (57, 35, 480, 480)
    scale = 0.62
    off = (262 - 306 * scale, 190 - 75 * scale)
    src = place(im, scale, off, art, 150)
    a = np.asarray(place(m, scale, off, art, 150)).astype(float) / 255
    subject = subject_layers("v3C", bw(src, a, s_curve=0.5, gamma=1.15), a, art, border_px=34, shadow_off=(4, 5))

    body = f"""
<defs>
  <radialGradient id="glow" cx="297" cy="300" r="470" gradientUnits="userSpaceOnUse">
    <stop offset="0" stop-color="{GREEN}" stop-opacity="0.85"/>
    <stop offset="0.45" stop-color="{GREEN}" stop-opacity="0.32"/>
    <stop offset="1" stop-color="{GREEN}" stop-opacity="0"/>
  </radialGradient>
  <clipPath id="art"><rect x="{art[0]}" y="{art[1]}" width="{art[2]}" height="{art[3]}" rx="5"/></clipPath>
</defs>
<rect x="{-B}" y="{-B}" width="{W + 2 * B}" height="{H + 2 * B}" fill="#070707"/>
<rect x="{-B}" y="{-B}" width="{W + 2 * B}" height="{H + 2 * B}" fill="url(#glow)"/>
<rect x="{art[0] + 6}" y="{art[1] + 10}" width="{art[2]}" height="{art[3]}" rx="5" fill="#000" opacity="0.45"/>
<g clip-path="url(#art)">
  <rect x="{art[0]}" y="{art[1]}" width="{art[2]}" height="{art[3]}" fill="{GREEN}"/>
  {t(W / 2, 0, "ZYON", 300, BLACK, "Anton", 400, "middle", ls=2, fit=440, captop=58)}
  {subject}
  {t(art[0] + 14, art[1] + art[3] - 14, "333", 26, BLACK, "Anton", 400)}
</g>
{starburst(512, 478, 58, 14, WHITE, BLACK, [("NEW", 30), ("MUSIC", 18)], stroke=BLACK)}
{grain_layer(grain_href(), B, W, H, tile=60, opacity=0.35)}
{t(537, 27, "PHOTO @FRAMED.BY.ICARUS", 7, GREY, weight=500, anchor="end", ls=0.6)}
{t(57, 562, "ZYON", 40, WHITE)}
{eq_bars(186, 560, 26, 5.5, GREEN)}
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
    page("v3_C_poster_A1_now_playing", W, H, B, body, guides)


# ---------------------------------------------------------------------------
# D) A1 riso - 8/8 photo on paper (halftone hides its camera shake), green sun, starburst
# ---------------------------------------------------------------------------
def riso():
    W, H, B = 594, 841, 3
    im, m = load("8_frontal_bw", choke=2)
    scale = 0.6
    off = (297 - 582 * scale, 72 - 65 * scale)
    rect = (-B, -B, W + 2 * B, 640)
    src = place(im, scale, off, rect, 40)
    a = np.asarray(place(m, scale, off, rect, 40)).astype(float) / 255
    L = lum(src, a, lo=1, hi=99.5, gamma=0.85, s_curve=0.3)
    L = L * a + (1 - a)                               # background -> paper
    fade = lambda u, v: 1 - smooth(0.64, 0.88, v)
    blk = halftone(1 - (1 - L) ** 1.8, rect, 2.6, BLACK, angle=45, invert=True, fade=fade, max_r=0.6)
    grn = halftone(1 - (1 - L) ** 0.8, rect, 3.3, GREEN, angle=15, invert=True, fade=fade, max_r=0.62)
    speck = grain_tile("grain_paper_v3", dark_only=True, strength=30)

    body = f"""
<rect x="{-B}" y="{-B}" width="{W + 2 * B}" height="{H + 2 * B}" fill="{PAPER}"/>
<g style="mix-blend-mode:multiply"><circle cx="297" cy="250" r="232" fill="{GREEN}"/></g>
<g style="mix-blend-mode:multiply" transform="translate(2.2 1.6)">{grn}</g>
<g style="mix-blend-mode:multiply">{blk}</g>
<g style="mix-blend-mode:multiply">{t(W / 2, 0, "ZYON", 300, GREEN, "Anton", 400, "middle", ls=2, fit=556, captop=392)}</g>
{outline(W / 2 + 4, 396, "ZYON", 556, BLACK, 1.6)}
{starburst(503, 88, 62, -14, BLACK, PAPER, [("NEW", 34), ("MUSIC", 21)])}
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
    page("v3_D_poster_A1_riso", W, H, B, body, guides)


if __name__ == "__main__":
    import sys
    todo = {"A": rollup, "B": pvc, "C": now_playing, "D": riso}
    for k in (sys.argv[1:] or todo):
        todo[k]()
        print("built", k)
