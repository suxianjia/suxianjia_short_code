from PIL import Image, ImageDraw

# 创建一个空白图像
size = 64  # 图标大小
image = Image.new("RGBA", (size, size), (255, 255, 255, 0))

# 绘制五角星
draw = ImageDraw.Draw(image)
points = [
    (32, 0), (39, 24), (63, 24), (43, 39),
    (51, 63), (32, 49), (13, 63), (21, 39),
    (1, 24), (25, 24)
]
draw.polygon(points, fill="yellow", outline="black")

# 保存为 favicon.ico
image.save("favicon.ico", format="ICO")