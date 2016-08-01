<%@ page import="java.io.*,java.util.*,org.apache.commons.codec.binary.Base64" %>
<%

    
    String error_msg = "{\"msg\": \"illegal\"}";
    String success_msg = "{\"msg\": \"legal\"}";

    response.setContentType("application/json; charset=UTF-8");
    // Base64.Decoder decoder = Base64.getDecoder();




    String query = request.getParameter("query");

    String filter[] = {
        "extractvalue",
        "updatexml",
        "select", 
        "union", 
        "from", 
        "and", 
        "or",
        "'", 
    };

    if ( query != null ){
        try {
            Boolean flag = false;
            query = new String(Base64.decodeBase64(query), "UTF-8");
            // check illigal
            for(int i=0; i<filter.length; i++){
                if (query.toLowerCase().contains(filter[i])){
                    flag = true;
                }
            }

            if (flag){
                out.print(error_msg);
            } else {
                out.print(success_msg);
            }
        } catch (Exception ex){
            out.print(error_msg);
        }
    } else {
        out.print(error_msg);
    }
%>