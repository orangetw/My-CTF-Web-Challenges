#include <stdio.h>
#include <stdlib.h>
#include <string.h>

void  CGI_INIT();
char* CGI_GET(char *);
void  do_job(char *, char *, char *);
void  CGI_GET_PASS(char *);

char secret_pass[25] = {0};

int get_input(char *username, char *password, char *job, char *action){
    char *data = NULL;

    // get username
    data = CGI_GET("username");
    if ( data == NULL ){
        goto FAILED;
    }
    sprintf(username, data);

    // get password
    data = CGI_GET("password");
    if ( data == NULL ){
        goto FAILED;
    }
    sprintf(password, data);
    
    // get job
    data = CGI_GET("job");
    if ( data == NULL ){
        goto FAILED;
    }
    sprintf(job, data);

    // get action
    data = CGI_GET("action");
    if ( data == NULL ){
        goto FAILED;
    }
    sprintf(action, data);

FAILED:
    return 1;

}

int main(){
    char job[16]      = {0};
    char password[32] = {0};
    char username[32] = {0};
    char action[48]   = {0};
    int flag=0, i=0;

    CGI_INIT();
    CGI_GET_PASS(secret_pass);

    get_input(username, password, job, action);

    char *needle = secret_pass;
    flag = 0, i = strlen(needle);
    do {
        if (!i)
            break;
        flag = (char)password[i] == needle[i];
        i--;
    } while(flag);

    if ( !flag ){
        puts("Auth Failed");
        return -1;
    } else {
        do_job(username, action, job);
        system("cat fake-flag");
    }

    return 0;
}