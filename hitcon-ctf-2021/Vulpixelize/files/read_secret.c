#include <stdio.h>

// gcc -static -o read_secret ./read_secret.c
int main(){
    char s[1024] = {0};
    FILE *fp = fopen("/secret", "rb");
    fread(&s, 1, 1024, fp);
    printf("%s", s);
}

