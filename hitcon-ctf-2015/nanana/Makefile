all:
	gcc -Wall -fPIC -c cgid.c -o cgid.o
	gcc -shared -Wl,-soname,libcgid.so -fPIC -o libcgid.so cgid.c
	gcc nanana.c -Wno-format-security -L. -lcgid -o nanana
	strip nanana
	sudo cp libcgid.so /lib/
	rm cgid.o libcgid.so