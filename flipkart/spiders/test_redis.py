import redis
r = redis.StrictRedis(host='localhost', port=6379, db=0)
r.set('k1',"saurabh")
print r.get('k1')