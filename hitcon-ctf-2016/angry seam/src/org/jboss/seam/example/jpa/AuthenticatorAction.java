package org.jboss.seam.example.jpa;

import static org.jboss.seam.ScopeType.SESSION;

import java.util.List;

import javax.persistence.EntityManager;

import org.jboss.seam.annotations.In;
import org.jboss.seam.annotations.Name;
import org.jboss.seam.annotations.Out;

@Name("authenticator")
public class AuthenticatorAction
{
   @In EntityManager em;
   
   @Out(required=false, scope = SESSION)
   private User user;
   
   public boolean authenticate()
   {
      List results = em.createQuery("select u from User u where u.username=#{identity.username} and u.password=#{identity.password}")
            .getResultList();
      
      if ( results.size()==0 )
      {
         return false;
      }
      else
      {
         user = (User) results.get(0);
         return true;
      }
   }

}
