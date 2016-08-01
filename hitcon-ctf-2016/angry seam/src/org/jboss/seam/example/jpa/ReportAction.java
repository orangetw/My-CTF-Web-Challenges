package org.jboss.seam.example.jpa;

import static org.jboss.seam.ScopeType.EVENT;

import javax.persistence.EntityManager;

import org.hibernate.validator.NotNull;
import org.hibernate.validator.Length;
import org.jboss.seam.annotations.In;
import org.jboss.seam.annotations.Name;
import org.jboss.seam.annotations.Out;
import org.jboss.seam.annotations.Scope;
import org.jboss.seam.faces.FacesMessages;


@Scope(EVENT)
@Name("reportAction")
public class ReportAction {

	   @In @Out
	   private User user;
	   
	   @In
	   private EntityManager em;
	   
	   private String badUrl;
	   
	   private Url url;

	   public void reportURL()
	   {
		   String username = user.getUsername();
		   
		   // check length
		   if (badUrl.length() < 5 || badUrl.length() > 256) {
			   FacesMessages.instance().add("URL too long.");
		   } else if (!badUrl.startsWith("http://52.198.197.227:8080/angryseam/")) {
			   FacesMessages.instance().add("Your URL seems not this site.");
		   } else {
			   url = new Url(username, this.badUrl);
			   em.persist(url);
			   FacesMessages.instance().add("Admin will see your URL soon.");   
		   }
	   }
	   
	   @NotNull
	   @Length(min=5, max=256)
	   public String getBadUrl()
	   {
	      return badUrl;
	   }

	   public void setBadUrl(String badUrl)
	   {
	      this.badUrl = badUrl;
	   }
	
}
