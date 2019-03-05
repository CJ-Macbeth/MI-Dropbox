import RPi.GPIO as GPIO
import time

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)

GPIO.setup(2, GPIO.OUT)
GPIO.setup(4, GPIO.OUT)

Red = GPIO.PWM(2, 50)
Blue = GPIO.PWM(4, 50)

Red.start(100)
Blue.start(100)

def Glow(x):
	d = 100
	while (d > 0):
		x.ChangeDutyCycle(d)
		d -= (100 - d) * .1 + .1
		time.sleep(.01)
	x.ChangeDutyCycle(0)

def Fade(x):
	d = 0
	while (d < 99.9999):
		x.ChangeDutyCycle(d)
		d += (100 - d) * .1 + .1
		time.sleep(.01)
	x.ChangeDutyCycle(100)

Glow(Red)
Glow(Blue)
Fade(Red)
Fade(Blue)

GPIO.cleanup()
