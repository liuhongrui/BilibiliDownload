# -*- coding: utf-8 -*-
# Created by SuperFashi
import requests
import random
import os
import sys
import re

APPKEY = {!!!'''APPKEY HERE'''!!!}

def read_cookie(cookiepath):
	cookies_file = open(cookiepath, 'r')
	cookies = cookies_file.readlines()
	cookies_file.close()
	ret = cookies[0].strip('\n').strip('\r')
	return ret

def fake_header():
	ip = random.randint(1,255)
	select = random.randint(1,2)
	if select == 1:
		ip = '220.181.111.' + str(ip)
	else:
		ip = '59.152.193.' + str(ip)
	fake = {
		'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.87 Safari/537.36',
		'Client-IP': ip,
		'X-Forwarded-For': ip
	}
	if os.path.exists('./bilicookies'):
		fake['Cookie'] = read_cookie('./bilicookies')
	return fake

def getURL(aid, pid):
	global APPKEY
	cid_args = {'type': 'json', 'id': aid, 'page': pid, 'appkey': APPKEY}
	ret_cid = requests.get('https://api.bilibili.com/view', params = cid_args, headers = fake_header())
	cid = ret_cid.json()['cid']
	media_args = {'otype': 'json', 'cid': cid, 'type': 'flv', 'quality': 4, 'appkey': APPKEY}
	ret_media = requests.get('http://interface.bilibili.com/playurl', params = media_args, headers = fake_header())
	response = ret_media.json()
	result = response['result']
	if result == 'error':
		return [{'url': 'error'}]
	return response['durl']

if __name__ == '__main__':
	if len(sys.argv) == 1:
		print('输入视频播放地址')
	else:
		url = sys.argv[1]
		regex_match = re.findall('http:/*[^/]+/video/av(\\d+)(/|/index.html|/index_(\\d+).html)?(\\?|#|$)',url)
		if not regex_match:
			print('error2')
			exit()
		aid = regex_match[0][0]
		pid = regex_match[0][2] or '1'
		try:
			media_urls = getURL(aid, pid)
		except Exception as e:
			media_urls = [{'url': 'error'}]
		for parts in media_urls:
			print(parts['url'])
