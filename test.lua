-- docker pull esolang/lua
-- docker run -it -v /home/dev/projects/slim-catalog:/scripts esolang/lua
-- lua /scripts/test.lua

arg[-3] = "lua"
arg[-2] = "-e"
arg[-1] = "sin=math.sin"
arg[0] = "script"
arg[1] = "a"
arg[2] = "b"

print(arg[-1])
return
