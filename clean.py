f = open( '/home/saurabh/flipkart/test_mix.json', 'r' )
f.seek(0)
w=open('/home/saurabh/flipkart/formatted_data.json','w')
i=1
for line  in f:
    if i==1:
        line=line[1:]
    lin=line[:-2]
    lin ='\n{"index":{"_id":"' + str(i) + '"}}\n' + lin
    i=i+1
    w.write(lin)
w.write('}')
f.close()
w.close()
