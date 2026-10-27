# ZYON — Spotify promo banners

Print-ready large-format artwork for ZYON (@zyon_333). Every QR code goes to https://linktr.ee/Zyonmusic. I checked each one by decoding it from the final PDF, for all three sets.

## v3 (use these): the loudest set

| File | Format | Trim size | Bleed | Photo | Concept |
|---|---|---|---|---|---|
| `print/v3/v3_A_rollup_850x2000mm.pdf` | Pull-up / roll-up stand | 850 × 2000 mm | 10 mm | 1 | Green field, his head over a giant black "ZYON", echo outlines, NEW MUSIC starburst, black player panel |
| `print/v3/v3_B_pvc_banner_1830x915mm.pdf` | PVC banner, 6 × 3 ft | 1830 × 915 mm | 10 mm | 4 | Green field, sound-wave rings coming off the mic, sticker-cut close-up |
| `print/v3/v3_C_poster_A1_now_playing.pdf` | A1 poster | 594 × 841 mm | 3 mm | 3 | Spotify "Now Playing" screen with a neon glow, sticker-cut cover, NEW sticker, equaliser bars |
| `print/v3/v3_D_poster_A1_riso.pdf` | A1 poster | 594 × 841 mm | 3 mm | 8 | Riso print: green "sun" behind his head, two-colour halftone, overprinted name |

What v3 adds to v2 to make it more eye-catching:
- **Big flat fields of Spotify green.** Bright colour carries further than dark, especially on a dark venue wall.
- **Black-and-white subject on green.** This is the highest-contrast pairing available.
- **Sticker-cut white border and drop shadow** round each cut-out, so the subject jumps forward.
- **Starburst "NEW MUSIC" stickers** as an instant focal point.
- **One extra device per layout:** sound-wave rings, echo type, the neon glow or the green sun.

The 8/8 photo has camera shake in the original. At roll-up size the upscaler turns that blur into streaks, so it's used on the riso poster, where the halftone dots hide it.

`previews/v3/` holds quick-look images; `_GUIDES` versions show the trim and safe zone. The older sets are in `print/v2/` and `print/v1/`.

## v2

Same four concepts with darker, green-duotone photos: `print/v2/`, `previews/v2/`.

## What changed from v1, and why

v1's weaknesses:
- The photos were coarse halftone made from low-res screenshots, so the faces turned to mush.
- Each layout was a photo with text stacked on it, with no depth.
- It looked flat and digital.
- There was nothing ownable about it.

v2's fixes:
- **Real photos.** Each source image was upscaled 4× with an AI model (Real-ESRGAN), and the subject was cut out with BiRefNet. The faces now hold up as photos.
  - The tonal treatment is Spotify-style: black → Spotify green `#1ED760` → mint highlight. That's the duotone language from Spotify's 2015 Collins rebrand, which was itself modelled on 60s concert posters ([Design Week](https://www.designweek.co.uk/spotify-undergoes-colourful-brand-refresh/), [Transform](https://www.transformmagazine.net/articles/2015/spotify-gets-visceral/)).
- **Depth through layering.** His head and cap sit in front of the giant name, with a soft contact shadow. It's the magazine-cover trick: the name is partly hidden but still reads instantly, and the figure jumps forward.
  - The type is oversized until the letters work as shapes. That's the dominant 2026 poster type trend, and one huge word reads at a glance from far away ([Jukebox Print](https://www.jukeboxprint.com/blog/20-of-the-biggest-graphic-design-trends-for-2026), [Figure Estudio](https://figureestudio.com/2026/09/10/typography-trends-late-2026/)).
- **Texture.** Film grain over everything. The riso poster uses real multiply overprint and a deliberate misregistration on the outline. Limited colour layers and visible texture are the screen-print look that suits collectible gig posters ([Made Good Designs](https://madegooddesigns.com/concert-poster-design/)).
- **An ownable motif.** Every design carries a Spotify scrubber stopped at **1:11 / 3:33**, a nod to his _333 handle. The Now Playing poster takes this all the way: the whole poster is a player screen, with "333" on the cover.
- **Spotify's own playbook.** RapCaviar's "Hip-Hop Needs New Leaders" campaign put one artist per city on billboards and posters: a big portrait, the name, a call to action ([Complex](https://www.complex.com/music/a/markelibert/spotify-rapcaviar-hip-hop-leaders-billboards)). The PVC banner follows that format.
- **Information hierarchy.** Name first, image second, then a tidy information block at the bottom with "LISTEN ON SPOTIFY" (one of Spotify's approved button phrases) and the QR code.
  - Signage rule of thumb: about 25 mm of letter height per 3 m of viewing distance ([banners.com](https://banners.com/c/how-far-away-can-someone-read-a-banner-complete-banner-font-size-guide)). The name is about 330 mm tall on the roll-up and about 480 mm on the PVC banner.

Typefaces: Anton (display), Space Grotesk (UI and body), Archivo Black (call to action), Space Mono (player data).

## Print resolution, straight

The photo layers are 75–150 DPI at final size. That's normal for large format viewed from more than 1–2 m ([4OVER4](https://www.4over4.com/faqs/banners/does-resolution-matter-for-large-banners-and-posters)). All type, the icons and the QR codes are vector, so they stay razor sharp.

The upscaling is AI, so it invents some fine detail: skin is a little smoothed when you stand up close. Original files from @framed.by.icarus would beat it. Put them into `source-photos/` and rebuild.

## Do these before printing

1. **Ask @framed.by.icarus for permission** to use the photos on printed ads, and for the original files if possible. Each design credits them.
2. **Confirm the artist name on Spotify** is "ZYON".
3. **Optional: add a Spotify Code** (from his Spotify profile: Share → the code under the image). I couldn't reach Spotify from this environment.
4. **Optional: put in a release name** in place of "NEW MUSIC · STREAMING NOW" if there's a single to push.

## Rebuilding

```
cd build
python3 prep_photos.py      # once: upscale + cut-outs (needs torch, spandrel, rembg; model links in the file)
python3 crop_screenshot.py shot.png name   # new IG screenshot -> source-photos/name.png
python3 build_v3.py         # writes v3_*.html + img/
node render.js v3_          # writes print/v3/*.pdf and previews/v3/*.jpg
python3 build_v2.py && node render.js v2_   # v2 set
python3 build.py && node render.js A_   # v1 set
```
