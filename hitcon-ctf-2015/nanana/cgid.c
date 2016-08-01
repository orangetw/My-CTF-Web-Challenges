#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <unistd.h>

char *query_string = NULL;

char from_hex(char ch) {
  return isdigit(ch) ? ch - '0' : tolower(ch) - 'a' + 10;
}

char* url_decode(char *str) {
  char *pstr = str, *buf = malloc(strlen(str) + 1), *pbuf = buf;
  while (*pstr) {
    if (*pstr == '%') {
      if (pstr[1] && pstr[2]) {
        *pbuf++ = from_hex(pstr[1]) << 4 | from_hex(pstr[2]);
        pstr += 2;
      }
    } else if (*pstr == '+') { 
      *pbuf++ = ' ';
    } else {
      *pbuf++ = *pstr;
    }
    pstr++;
  }
  *pbuf = '\0';
  return buf;
}

void CGI_INIT(){
    dup2(1, 2);
    puts("Content-Type: text/plain;charset=UTF-8\n");
}

char* CGI_GET(char *name){
    if ( query_string == NULL ){
        query_string = getenv("QUERY_STRING");
        if (query_string == NULL){
            return NULL;
        }
    }

    char *env = strdup(query_string);
    char *key   = malloc(4096);
    char *value = malloc(4096);
    char *splitted = strtok(env, "&");
    while (splitted != NULL){
        sscanf(splitted , "%[^=]=%s", key, value);

        if ( strcmp(name, key) == 0 ){
            return url_decode(value);
        }
        splitted = strtok(NULL, "&");
    }

    return NULL;
}

void do_job(char *b, char *c, char *d){
    puts("get shell, plz");
}

void CGI_GET_PASS(char *pass){
    strncpy(pass, "hitconctf2015givemeshell", 25);
}
