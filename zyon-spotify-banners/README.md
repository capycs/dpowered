# ZYON — Spotify promo banners

Print-ready large-format artwork for ZYON (@zyon_333). Every QR code goes to https://linktr.ee/Zyonmusic. I checked each one by decoding it from the final PDF.

## v2 (use these)

| File | Format | Trim size | Bleed | Concept |
|---|---|---|---|---|
| `print/v2/v2_A_rollup_850x2000mm.pdf` | Pull-up / roll-up stand | 850 × 2000 mm | 10 mm | Magazine cover: his head in front of a giant "ZYON" |
| `print/v2/v2_B_pvc_banner_1830x915mm.pdf` | PVC banner with eyelets, 6 × 3 ft | 1830 × 915 mm | 10 mm | Billboard: green duotone close-up, his cap tucks over the N |
| `print/v2/v2_C_poster_A1_now_playing.pdf` | A1 poster | 594 × 841 mm | 3 mm | The whole poster is a Spotify "Now Playing" screen, with a designed cover |
| `print/v2/v2_D_poster_A1_riso.pdf` | A1 poster | 594 × 841 mm | 3 mm | Two-colour riso print, black + green overprint on paper stock |

`previews/v2/` holds quick-look images. The `_GUIDES` versions show the trim line in magenta and the safe zone as a dashed line.

The v1 set (halftone-only) is still in `print/v1/` and `previews/v1/`.

Send the printer the PDF and tell them the trim size. The background runs into the bleed. All text and QR codes sit inside the safe zone:
- Roll-up: the bottom 150 mm is kept clear because the stand's base hides it.
- PVC: everything is 50 mm or more from the edges, clear of hems and eyelets.

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
python3 build_v2.py         # writes v2_*.html + img/
node render.js v2_          # writes print/v2/*.pdf and previews/v2/*.jpg
python3 build.py && node render.js A_   # v1 set
```
