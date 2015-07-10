f = open( '/home/saurabh/flipkart/test_mix.json', 'r' )
n=sum(1 for line in f)
f.close()
print n 
f = open( '/home/saurabh/flipkart/test_mix.json', 'r' )
w=open('/home/saurabh/flipkart/formatted_data.json','w')
i=1
for line  in f:
    line ='{"index":{"_id":"' + str(i) + '"}}\n' + line
    #ini='{index":{"_id":"8"}}\n'
    #line=ini+line
    print line
    i=i+1
    w.write(line)
f.close()
w.close()
