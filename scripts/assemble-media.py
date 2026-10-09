from pathlib import Path
import hashlib, json
root = Path(__file__).resolve().parent.parent
manifest = json.loads((root / 'asset-manifest.json').read_text())
for name, record in manifest.items():
    target = root / name
    parts = sorted((root / '.media-parts').glob(target.name + '.part*'))
    if not target.exists():
        if not parts:
            raise RuntimeError('Missing media: ' + name)
        data = b''.join(p.read_bytes() for p in parts)
        if len(data) != record['bytes'] or hashlib.sha256(data).hexdigest() != record['sha256']:
            raise RuntimeError('Media verification failed: ' + name)
        target.write_bytes(data)
    if hashlib.sha256(target.read_bytes()).hexdigest() != record['sha256']:
        raise RuntimeError('Media verification failed: ' + name)
    for part in parts:
        part.unlink()
print('Approved media assembled and verified.')
