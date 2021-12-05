#include <stdio.h>

// gcc -static -o readflag ./readflag.c
int main(){
    char s[1024] = {0};
    FILE *fp = fopen("/flag", "rb");
    fread(&s, 1, 1024, fp);
    printf("%s", s);
}
