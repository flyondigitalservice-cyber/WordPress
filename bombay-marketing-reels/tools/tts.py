"""Run the library's edge-tts tool behind a TLS-inspecting proxy: trust the CA bundle named in SSL_CERT_FILE
(edge-tts otherwise reads only certifi's own list).   python tools/tts.py <LIB> lines.json out_dir"""
import os, sys, runpy
ca = os.environ.get('SSL_CERT_FILE') or os.environ.get('REQUESTS_CA_BUNDLE')
if ca and os.path.exists(ca):
    import certifi
    certifi.where = lambda: ca
lib = sys.argv[1]
sys.argv = [os.path.join(lib, 'core/tts/tts_zh.py')] + sys.argv[2:]
runpy.run_path(sys.argv[0], run_name='__main__')
