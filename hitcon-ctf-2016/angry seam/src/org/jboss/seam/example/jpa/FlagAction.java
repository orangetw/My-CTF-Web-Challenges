package org.jboss.seam.example.jpa;

import java.lang.ProcessBuilder;
import java.io.*;
import static org.jboss.seam.ScopeType.EVENT;

import javax.persistence.EntityManager;

import org.jboss.seam.annotations.In;
import org.jboss.seam.annotations.Name;
import org.jboss.seam.annotations.Scope;
import org.jboss.seam.annotations.web.RequestParameter;  
import org.jboss.seam.faces.FacesMessages;

@Scope(EVENT)
@Name("flag")
public class FlagAction
{

   @In
   private User user;
   
   @In
   private EntityManager em;

   
   public boolean isAdmin() {
	   boolean flag = false;
	   
	   if ( user.getUsername().length() == 5 && user.getUsername().equals("admin") ) {
		   flag = true;
	   }
	   
	   return flag;
	   
   }
   
   public String getFlag() throws IOException
   {
	   
	   ProcessBuilder pb = new ProcessBuilder("/readflag");

	    Process p = pb.start();
	    InputStream is = p.getInputStream();
	    BufferedReader br = new BufferedReader(new InputStreamReader(is));
	    StringBuilder sb = new StringBuilder(); 
	    String line = null;
	    while ((line = br.readLine()) != null) {
	      sb.append(line);
	    }

      return sb.toString();
   }
   
}
