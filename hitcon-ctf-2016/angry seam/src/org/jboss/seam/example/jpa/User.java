//$Id: User.java 3176 2007-01-09 20:53:45Z myuan $
package org.jboss.seam.example.jpa;

import static org.jboss.seam.ScopeType.SESSION;

import java.io.Serializable;

import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.Table;

import org.hibernate.validator.Length;
import org.hibernate.validator.NotNull;
import org.hibernate.validator.Pattern;
import org.jboss.seam.annotations.In;
import org.jboss.seam.annotations.Name;
import org.jboss.seam.annotations.Scope;

@Entity
@Name("user")
@Scope(SESSION)
@Table(name="user")
public class User implements Serializable
{

	
   private String username;
   private String password;
   private String name;
   private String description;
   
   public User(String name, String password, String username, String description)
   {
      this.name = name;
      this.description = description;
      this.password = password;
      this.username = username;
   }
   
   public User() {}

   @NotNull
   @Length(max=100)
   public String getName()
   {
      return name;
   }
   public void setName(String name)
   {
      this.name = name;
   }
   
   @NotNull
   @Length(min=5, max=15)
   public String getPassword()
   {
      return password;
   }
   public void setPassword(String password)
   {
      this.password = password;
   }
   
   @Id 
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
   
   @Length(max=100)
   public String getDescription()
   {
      return description;
   }
   public void setDescription(String description)
   {
      this.description = description;
   }
   
   
   @Override
   public String toString() 
   {
      return "User(" + username + ")";
   }
}
