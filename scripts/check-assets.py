from pathlib import Path
import hashlib, json, re
root = Path(__file__).resolve().parent.parent
manifest = json.loads((root / 'asset-manifest.json').read_text())
for name, expected in manifest.items():
    data = (root / name).read_bytes()
    assert len(data) == expected['bytes'], name
    assert hashlib.sha256(data).hexdigest() == expected['sha256'], name
for page in ('index.html', 'site.html'):
    for path in re.findall(r'assets/[a-f0-9]+\.(?:jpg|png|svg|mp4)', (root / page).read_text()):
        assert path in manifest, path
assert sum(v['type'] == 'video/mp4' for v in manifest.values()) == 2
print('All media hashes and page references verified; two approved films present.')
