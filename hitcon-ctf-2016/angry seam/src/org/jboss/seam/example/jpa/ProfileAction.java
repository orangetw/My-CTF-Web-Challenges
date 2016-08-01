package org.jboss.seam.example.jpa;

import static org.jboss.seam.ScopeType.EVENT;

import javax.persistence.EntityManager;

import org.jboss.seam.annotations.In;
import org.jboss.seam.annotations.Name;
import org.jboss.seam.annotations.Out;
import org.jboss.seam.annotations.Scope;
import org.jboss.seam.annotations.web.RequestParameter;  
import org.jboss.seam.faces.FacesMessages;

@Scope(EVENT)
@Name("profile")
public class ProfileAction
{

   @In @Out
   private User user;
   
   @In
   private EntityManager em;
   
   @RequestParameter
   String username;
   
   
   @Out
   private String outUsername = "";
   @Out
   private String outDescription = "";
   
   private boolean changed;
   
   public void changeProfile()
   {
     user = em.merge(user);
     FacesMessages.instance().add("Description updated");
     changed = true;
   }
   
   public boolean isChanged()
   {
      return changed;
   }
   
   public void getProfile() {	   
	   if ( username != null && !username.equals("")) {
		   User _user = em.find(User.class, username);
		   if (_user != null){
			   outUsername = _user.getName();
			   outDescription = _user.getDescription();
		   } else {
			   outUsername = user.getName();
			   outDescription = user.getDescription();			   			   
		   }   
	   } else {
		   outUsername = user.getName();
		   outDescription = user.getDescription();
	   }
	   
	   if (outUsername == null){
		   outUsername = "No such user.";
	   }
	   if (outDescription == null) {
		   outDescription = "No desc.";
	   }
	   
	   
   }
   
   public String getOutDescription(){
	   return outDescription;
   }
   public String getOutUsername(){
	   return outUsername;
   }
}
