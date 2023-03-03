#!C:\Users\Prichici\AppData\Local\Programs\Python\Python310\python.exe
import sys,json
import requests
import redis as red
result = sys.argv[1]
#For details about this issue verify the "Challanges" chapter;
#In short terms, there is a problem when passing json from php cu python
x = result[1:len(result)-1].split(",")

#Splitting the data in significant variables
fname=x[0]
lname=x[1]
#the key for each element of the hash table will be formatted like this:
user="user:"+x[2]
email=x[3]
psd=x[4]
cnf_psd=x[5]

#Connecting to the redis server; creating the redis client
redis = red.Redis(host='localhost', port=6379, db=0, charset="utf-8", decode_responses=True)
#In order to get all the data in the database we have to declare a different redis client that accepts the scan() function
r = red.StrictRedis(host='localhost', port=6379, db=0, charset="utf-8", decode_responses=True)

flag=1
#Search the user in the database
if redis.hexists(user,'password')==True:
    print("THIS USER ALREADY EXISTS")
else:
    if psd!=cnf_psd:
        print("PSD DOESNT MATCH")
    else:
         #Here we use the strict client; we are looking only to the hashtables whose key is like "user:"
         for key in r.scan_iter("user:*"):
            if redis.hget(key,'email')==email:
                print("EMAIL IN USE")
                flag=0
                break
         if flag==1:
             #add in the redis db
             redis.hmset(user, {'firstname':fname, 'lastname':lname, 'email':email, 'password':psd})
             #By sending '1' to php as a response, the writing is marked as done
             print(1)
             #add in the API via requests package
             user={"username":x[2], "connected":bool(False), "conv_id":0}
             r=requests.post('https://63ef781c4d5eb64db0ca0583.mockapi.io/user', data=user)
             print(r.text)
         
        
    
