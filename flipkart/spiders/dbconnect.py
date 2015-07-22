import MySQLdb

db = MySQLdb.connect(host="localhost", # your host, usually localhost
                     user="root", # your username
                      passwd="9595", # your password
                      db="flipkart_master") # name of the data base

# you must create a Cursor object. It will let
#  you execute all the queries you need
#cursor = db.cursor() 

# Use all the SQL you like
#cursor.execute("SELECT * FROM sau")

# print all the first cell of all the rows
#for row in cursor.fetchall() :
#    print row[0]