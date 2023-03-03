#!C:\Users\Prichici\AppData\Local\Programs\Python\Python310\python.exe

import sys,json
import datetime
import requests
import pymongo
import datetime
from datetime import date

#Connecting to the client
client = pymongo.MongoClient("mongodb://localhost:27017/")
db = client["Project"]
msg = db["messages"]
url='https://63ef781c4d5eb64db0ca0583.mockapi.io/conversation'
result = sys.argv[1]
print(result)
x = result[1:len(result)-1].split(",")
print(x)

text=x[0]
id_r=int(x[1])
id_s=int(x[2])
id_conv=int(x[3])
d1 = datetime.datetime.now()
hour=str(d1.hour)+':'+str(d1.minute)
user=[{"user":id_s, "text":text, 'hour': hour,'date':date.today().strftime("%d/%m/%Y")}]
#if the 4th element(the conv id) in x equals 0 => create a new conversation
if id_conv==0:
    messages={"Messages": [{"user":id_s, "text":text, "hour": hour,"date":date.today().strftime("%d/%m/%Y")}],"Sender": id_s, "Receiver": id_r}
    messages=json.dumps(messages)
    headers = {'Content-Type': 'application/json', 'Accept':'application/json'}
    r=requests.post(url, data=messages,headers=headers)
    conv=json.loads(r.text)
    print(conv['id'])
    newvalues={"_id":int(conv['id']), "messages":user, "sender":id_s, "receiver":id_r}
    msg.insert_one(newvalues)
else:
    headers = {'Content-Type': 'application/json', 'Accept':'application/json'}
    r=requests.get(url+'/'+str(id_conv),headers=headers)
    m=json.loads(r.text)
    n=m['Messages']
    n+=user
    newvalues = { "$set": { "messages": n } }
    print(newvalues)
    n=json.dumps(n)
    messages='{"Messages":'
    for i in n:
        messages+=str(i)
    messages+='}'
    print(messages)
    r=requests.put(url+'/'+str(id_conv), data=messages, headers=headers)
    print(r.text)
    mess = { "_id": int(id_conv) }
    msg.update_one(mess,newvalues)

#if not, get the conversation by id from API

#append the message

#update the database


#mess = { "_id": 2 }
#newvalues = { "$set": { "messages": Messages } }

#msg.update_one(mess,newvalues)



