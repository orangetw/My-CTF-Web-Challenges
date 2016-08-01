<%@ page import="java.io.*,java.util.*" %>
<%
    response.setContentType("application/json; charset=UTF-8");
    Base64.Decoder decoder = Base64.getDecoder();
    String username = request.getParameter("username");
    String password = request.getParameter("password");
    
    if ( username != null && password != null ) {
        if (username.equals("admin") && password.equals("12345678")){
            out.println("{\"msg\": \"congrats, login ok. But did you really need to login?\"}");
        } else {
            out.println("{\"msg\":\"login failed\"}");
        }
    } else {
        out.println("{\"msg\":\"nothing happened\"}");
    }
%>