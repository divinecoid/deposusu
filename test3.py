import urllib.request
req = urllib.request.Request("http://127.0.0.1:8000/test-layout-2")
try:
    html = urllib.request.urlopen(req).read().decode('utf-8')
    print("Found @media print:", "@media print" in html)
    print("Found no-print header:", "class=\"no-print" in html)
    print("Found Finance menu:", "Finance" in html)
    print("Found Order Management:", "Order Management" in html)
except Exception as e:
    print(e)
