#coding=utf-8
from urllib import request
from bs4 import BeautifulSoup
import re
#根据昵称获取个人信息
def getUserInfo(nickname):
    #csdn禁止爬虫,模拟浏览器发送请求
    req = request.Request('http://my.csdn.net/' + nickname)
    req.add_header('User-Agent', 'Mozilla/6.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/8.0 Mobile/10A5376e Safari/8536.25')
    with request.urlopen(req) as f:
        data = f.read()
        html = data.decode('UTF-8')
        soup = BeautifulSoup(html,'html.parser')
        #爬取的数据
        sresult = nickname
        uname = soup.select(".person-nick-name span")
        if len(uname) > 0:
            sresult += '--' + uname[0].string
            pass
        
        #print(soup.select(".person-detail")[0].string)
        #去除多余空白内容stripped_strings
        for string in soup.select(".person-detail")[0].stripped_strings :
            if re.match("[^|]",string) != None:
                sresult += '--' + string
                pass
            pass
        f = open("info.txt","a+",encoding='utf-8')
        f.write(sresult+'\r\n')  
        f.close()
        #已抓取过的用户存入文件
        fu = open("user.txt","a+",encoding='utf-8')
        fu.write(nickname+' ')  
        fu.close()
        pass

#获取关注者
def getFollowers(nickname):
    #初始化set变量存放将要爬取的用户昵称
    willGet = set([])
    global nickNo
    #csdn禁止爬虫,模拟浏览器发送请求
    req = request.Request('http://my.csdn.net/' + nickname)
    req.add_header('User-Agent', 'Mozilla/6.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/8.0 Mobile/10A5376e Safari/8536.25')
    with request.urlopen(req) as f:
        data = f.read()
        html = data.decode('UTF-8')
        soup = BeautifulSoup(html,'html.parser')
        
        for tag in soup.select('.header a') :
            name = tag.attrs['href']
            if existName(name) == 0:
                willGet.add(name)
    pass
    #先把当前用户可抓取的用户信息拿到
    for name in list(willGet):
        if existName(name) == 0:
            getUserInfo(name)
            #willGet.remove(name)
            print('抓取'+ name +'信息成功\r\n')
            nickNo.append(name)
            pass
        pass
    #再延伸爬取
    if(len(willGet)):
        for un in nickNo:
            getFollowers(un)
            pass
            
#检查是否已爬取过
def existName(name):
    f = open("user.txt","r+")
    data = f.read()
    return data.count(name)
    pass

if __name__=='__main__':
    nickNo = []
    getFollowers('free_ant')
