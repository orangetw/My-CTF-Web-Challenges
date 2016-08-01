package org.jboss.seam.example.jpa;

import java.net.URLDecoder;
import java.io.*;

import static org.jboss.seam.ScopeType.EVENT;


import org.jboss.seam.annotations.Name;
import org.jboss.seam.annotations.Scope;
import org.jboss.seam.annotations.web.RequestParameter;  


@Scope(EVENT)
@Name("util")
public class Util {
	   @RequestParameter
	   String location = "";
	
	   public String escape(String s) {
		   try {
			   s = URLDecoder.decode(s);
			   s = s.replaceAll(">", "").replaceAll("<", "").replaceAll("\"", "");
			   return s;
		   } catch (IllegalArgumentException e ) {   
			   return "";
		   }
	   }
	  
	   public String getCSS(){
		   if (location == null || location.equals("")){
			   location = "user.css";
		   }
		   
		   InputStream input = this.getClass().getClassLoader().getResourceAsStream("/resource/" + location);
		   String line = null;
		   StringBuilder sb = new StringBuilder();
		   
		   BufferedReader br = new BufferedReader(new InputStreamReader(input));
		   try {
			   while((line = br.readLine()) != null) {
				   sb.append(line);
			   }
			   
			   br.close();
		   } catch(IOException  e){
			   e.printStackTrace();  
		   } 
		   
		   return sb.toString();
	   }
	   	
}
