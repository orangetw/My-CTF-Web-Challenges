//$Id: User.java 3176 2007-01-09 20:53:45Z myuan $
package org.jboss.seam.example.jpa;

import java.io.Serializable;

import javax.persistence.Entity;
import javax.persistence.GeneratedValue;
import javax.persistence.Id;
import javax.persistence.Table;

import org.hibernate.validator.Length;
import org.hibernate.validator.Pattern;
import org.jboss.seam.annotations.Name;

@Entity
@Name("url")
@Table(name="url")
public class Url implements Serializable
{
   
   private Long id;
   private String username;
   private String url;
   
   public Url(String username, String url)
   {
      this.username = username;
      this.url = url;
   }
   
   public Url() {}

   @Id @GeneratedValue
   public Long getId()
   {
      return id;
   }
   public void setId(Long id)
   {
      this.id = id;
   }
   
   
   @Length(min=5, max=15)
   @Pattern(regex="^\\w*$", message="not a valid username")
   public String getUsername()
   {
      return username;
   }
   public void setUsername(String username)
   {
      this.username = username;
   }
   
   public String getUrl()
   {
      return url;
   }
   public void setUrl(String url)
   {
      this.url = url;
   }
   
   @Override
   public String toString() 
   {
      return "User(" + username + ")";
   }
}
