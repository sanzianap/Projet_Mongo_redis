import requests, json
import datetime
from datetime import date

id_conv=1
x = datetime.datetime.now()
hour=str(x.hour)+':'+str(x.minute)
url='https://63ef781c4d5eb64db0ca0583.mockapi.io/conversation'
user={"user":2, "text":'Salut', 'hour': hour,
       'date':date.today().strftime("%d/%m/%Y")}

headers = {'Content-Type': 'application/json', 'Accept':'application/json'}
r=requests.get(url+'/'+str(id_conv),headers=headers)
m=json.loads(r.text)
n=m['Messages']
n+=[user]
n=json.dumps(n)
messages='{"Messages":'
for i in n:
  messages+=str(i)
messages+='}'
print(n)
print(messages)
#r=requests.put(url+'/'+str(id_conv), data=messages, headers=headers)
#conv=json.loads(r.text)
#print(conv['id'])
#mess = { "_id": int(conv['id']) }
#newvalues = { "$set": { "messages": n } }
#msg.update_one(mess,newvalues)
