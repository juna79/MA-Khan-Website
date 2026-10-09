from pathlib import Path
import zipfile
root = Path(__file__).resolve().parent.parent
out = root / 'build' / 'makhan-theme.zip'
out.parent.mkdir(exist_ok=True)
with zipfile.ZipFile(out, 'w', zipfile.ZIP_DEFLATED) as package:
    for name in ('index.php', 'functions.php', 'github-updates.php', 'style.css', 'site.html'):
        package.write(root / name, 'makhan/' + name)
    for path in sorted((root / 'assets').iterdir()):
        package.write(path, 'makhan/assets/' + path.name)
print(out)
