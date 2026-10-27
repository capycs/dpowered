# ZYON — Spotify promo banners

Print-ready large-format artwork for ZYON (@zyon_333). Every banner's QR code points to https://linktr.ee/Zyonmusic. I checked each one by decoding it from the final PDF.

## Files

| File | Format | Trim size | Bleed | Photo |
|---|---|---|---|---|
| `print/A_rollup_850x2000mm.pdf` | Pull-up / roll-up stand | 850 × 2000 mm | 10 mm | 1 (cut-out) |
| `print/B_pvc_banner_1830x915mm.pdf` | PVC banner with eyelets (6 × 3 ft) | 1830 × 915 mm | 10 mm | 4 (close-up) |
| `print/C_poster_A1_594x841mm.pdf` | A1 poster (scales to A0 cleanly) | 594 × 841 mm | 3 mm | 3 (on stage) |

- `previews/*.jpg`: quick-look images. The `_GUIDES` versions show the trim line in magenta and the safe zone as a dashed line.
- `source-photos/`: the four photos cropped out of the Instagram screenshots, with the IG overlays (`1/8` badge, mute and tag icons) painted out.
- `build/`: the generator. Run `python3 build.py && node render.js` to rebuild after editing text or layout.

Send the printer the PDF and tell them the trim size. The background runs into the bleed. All text and the QR code sit inside the safe zone:
- Roll-up: the bottom 150 mm is kept clear because the stand's base hides it.
- PVC: everything is 50 mm or more from the edges, clear of hems and eyelets.

## Design decisions

- **Spotify palette only.** The only colours are Spotify green `#1ED760` (the current brand hex), near-black and white. People link green on black to Spotify before they read a word, so the banner says "streaming" from across a room.
- **Photos as vector halftone dots.** The only photos available are phone screenshots, about 1179 px wide. Printed plain at 1.8 m that works out to roughly 25 DPI, which is soft and blocky. Each photo is rebuilt as thousands of vector dots at a 45° screen angle, so the edges stay sharp at any size. The halftone also looks like a deliberate choice: it's the gig-poster / risograph look, and it continues the duotone style from Spotify's 2015 rebrand by Collins, which was itself modelled on 60s album covers and concert posters.
- **One-word hero.** "ZYON" in Anton, a heavy condensed display face, runs the full width of every layout. The usual signage rule is about 1 inch (25 mm) of letter height per 10 ft (3 m) of viewing distance. On the roll-up the name is about 330 mm tall, so it reads from the back of a venue. The supporting text is Space Grotesk Bold.
- **Three layouts, three ways of using the colour.** The roll-up is green dots on black. The PVC banner is a green field with black dots, which shouts loudest on a railing or a wall. The poster is a white halftone with an angled green band. The angled crop is another nod to Spotify's campaign style.
- **Clear call to action.** Each layout carries "LISTEN ON SPOTIFY", one of Spotify's approved button phrases, plus a large "SCAN TO STREAM" QR code. The QR codes are 120–220 mm wide, so they scan from a few metres away. All of them are dark-on-light, because some Android cameras can't read inverted codes.
- **Photographer credit.** Each banner carries "Photo: @framed.by.icarus". The photos are theirs.

## Do these before printing

1. **Get the original photo files from @framed.by.icarus**, along with permission to use them on print ads. Originals give a cleaner halftone. If you'd rather use straight photographic versions, you need originals: screenshots can't hold up at banner size.
2. **Confirm the artist name on Spotify** is "ZYON" exactly as written here.
3. **Optional: add a Spotify Code.** From his Spotify profile, tap ⋯ → Share → the code under the cover art, or use spotifycodes.com. It goes next to the QR and scans straight into Spotify. I couldn't reach Spotify from this environment, so it isn't included.
4. **Optional: put in a release name.** If there's a specific single to push, it can replace "NEW MUSIC · STREAMING NOW".
5. The Spotify icon is the standard brand mark, left unaltered. For anything official, Spotify for Artists has the approved logo files.
