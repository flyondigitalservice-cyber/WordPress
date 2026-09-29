import json,sys
m=json.load(open('out/map.json')); new=json.load(sys.stdin); m.update(new)
json.dump(m,open('out/map.json','w'),indent=0)
print(len(m),'mapped;', 'errors:',[k for k,v in new.items() if 'error' in v])
