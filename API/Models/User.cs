namespace API.Models
{
    using System;
    using System.Collections.Generic;
    using System.ComponentModel.DataAnnotations;
    using System.ComponentModel.DataAnnotations.Schema;
    using System.Data.Entity.Spatial;

    public partial class User
    {
        public User()
        {
            CompletedSurveys = new HashSet<CompletedSurvey>();
        }

        [Required]
        public int Id { get; set; }

        [Required]
        public int Subject { get; set; }

        [Required]
        [StringLength(25)]
        public string Username { get; set; }

        [Required]
        public string Password { get; set; }

        [Required]
        public int Admin { get; set; }

        public virtual ApiKey ApiKey { get; set; }

        public virtual ICollection<CompletedSurvey> CompletedSurveys { get; set; }
    }
}
