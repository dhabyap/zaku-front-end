from PIL import Image, ImageDraw, ImageFont
import os

os.makedirs('icons', exist_ok=True)

BG = '#0E0E0E'
YELLOW = '#FFD600'
ORANGE = '#FF4800'

sizes = [72, 96, 128, 144, 152, 192, 384, 512]

for size in sizes:
    img = Image.new('RGBA', (size, size), BG)
    d = ImageDraw.Draw(img)
    s = size / 64
    bw = max(1, int(2.5 * s))
    off = max(1, int(5 * s))
    d.rectangle([off, off, off + size - 2*off, off + size - 2*off], outline=YELLOW, width=bw)
    d.rectangle([int(2*s), int(2*s), size - int(2*s), size - int(2*s)], outline=YELLOW, width=bw)
    d.rectangle([int(2*s), int(2*s), int(16*s), int(7*s)], fill=ORANGE)
    d.rectangle([size - int(22*s), size - int(13*s), size - int(8*s), size - int(8*s)], fill=ORANGE)
    lw = max(1, int(2.5 * s))
    d.line([int(8*s), int(38*s), int(21*s), int(28*s)], fill=YELLOW, width=lw)
    d.line([int(10*s), int(33*s), int(22*s), int(25*s)], fill=YELLOW, width=max(1, int(2*s)))
    d.line([int(13*s), int(29*s), int(23*s), int(23*s)], fill=YELLOW, width=max(1, int(1.5*s)))
    d.line([size - int(8*s), int(38*s), size - int(21*s), int(28*s)], fill=YELLOW, width=lw)
    d.line([size - int(10*s), int(33*s), size - int(22*s), int(25*s)], fill=YELLOW, width=max(1, int(2*s)))
    d.line([size - int(13*s), int(29*s), size - int(23*s), int(23*s)], fill=YELLOW, width=max(1, int(1.5*s)))
    nx, ny, nw, nh = int(20*s), int(27*s), int(24*s), int(14*s)
    d.rounded_rectangle([nx, ny, nx+nw, ny+nh], radius=max(1, int(2*s)), fill=ORANGE)
    try:
        f = ImageFont.truetype('arialbd.ttf', max(8, int(13*s)))
    except:
        f = ImageFont.load_default()
    d.text((size/2, ny+nh/2), '$', fill=YELLOW, font=f, anchor='mm')
    ds = max(3, int(6*s))
    d.rectangle([size - int(16*s), int(6*s), size - int(10*s), int(12*s)], fill=ORANGE)
    d.rectangle([int(10*s), size - int(14*s), int(16*s), size - int(8*s)], fill=YELLOW)
    img.save(f'icons/icon-{size}.png')
    print(f'Created icon-{size}.png')

print('Done')