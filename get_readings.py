#!/usr/bin/python

import getopt
import sys
import serial
import re
import sqlite3 as lite
from datetime import datetime

def main():
	
	port = '/dev/ttyUSB0'
	baud = 57600
	timeout = 10
	
	try:
		opts, args = getopt.getopt(sys.argv[1:], "t:p:b:o:h", ["help"])
	except getopt.GetoptError, err:
		print str(err)
		sys.exit()

	for o, a in opts:
		if o == "-t":
			timeout = int(a)
		elif o == "-p":
			port = a
		elif o == "-b":
			baud = int(a)
		elif o in ("-h", "--help"):
			usage()
			sys.exit()
		elif o == "-o":
			format = a
		else:
			sys.exit()
			
			
			
	meter = serial.Serial(port, baud, timeout=timeout)
	#meter.open()
	
	data = meter.readline()
	#print data
	meter.close()
	
	watts_ex = re.compile('<watts>([0-9]+)</watts>')
	temp_ex = re.compile('<tmpr>([0-9\.]+)</tmpr>')
	time_ex = re.compile('<time>([0-9\.\:]+)</time>')
	if len(watts_ex.findall(data)) > 0:
	
		watts = str(int(watts_ex.findall(data)[0])) # cast to and from int to strip leading zeros
	temp = temp_ex.findall(data)[0]
	time = datetime.now()
	
	#Save in database
	con = lite.connect('/home/pi/www/EnergyDB/energymonitor.db')
	
	with con:
	    
	    cur = con.cursor()   
	     
	    #Create a READINGS table if it doesn't already exist
	    cur.execute('CREATE TABLE IF NOT EXISTS readings (r_datetime TIMESTAMP, r_watts INT, r_temp INT)')
	    
	    cur.execute('INSERT INTO readings VALUES(?,?,?)', (time, watts, temp))
	
if __name__ == "__main__":
	main()
