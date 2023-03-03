#!C:\Users\Prichici\AppData\Local\Programs\Python\Python310\python.exe
import sys,json
import redis as red
import datetime
import requests

#Retrieving the data sent from php
result = sys.argv[1]
#Result: an array with the username and the password
#For details about this issue verify the "Challanges" chapter;
#In short terms, there is a problem when passing json from php cu python
x = result[1:len(result)-1].split(",")

#Connecting to the redis server; creating the redis client
redis = red.Redis(host='localhost', port=6379, db=0, charset="utf-8", decode_responses=True)
#Saving the API's url for future uses
url='https://63ef781c4d5eb64db0ca0583.mockapi.io/user'

#Splitting the data in significant variables
user="user:"+x[0]
psd=x[1]

#Search the user in the database in order to verify the coherence of the data
#By print(), we send a response to php
#If the answer equals 0, we know that the user isn't valid (the reason is not that important)
if redis.hexists(user,'password')==False:
    #if it doesn't exists in the database
    print(0)
else:
    if redis.hget(user,'password')!= psd:
        #if the password doesn't match the one in the database
        print(0)
    else:
        #if it reached this point => user OK
        print(1)
        #in order to update the status of the connection, we have to get the array of users and search the user by username
        r=requests.get(url)
        f=r.json()
        for i in f:
            if i['username']==x[0]:
                user={'connected':bool(True)}
                r=requests.put(url+'/'+i['id'], data=user)
                #we send the id too because it will be helpful when we'll search for the coversation 
                print(i['id'])
        
    
   
