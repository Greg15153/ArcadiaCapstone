namespace WebAPI.Models
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
            ApiKeys = new HashSet<ApiKey>();
        }

        public int Id { get; set; }

        [StringLength(25)]
        public string Username { get; set; }

        [Required]
        public string Password { get; set; }

        public int Admin { get; set; }

        public int subject { get; set; }

        public virtual ICollection<CompletedSurvey> CompletedSurveys { get; set; }

        public virtual ICollection<ApiKey> ApiKeys { get; set; }
    }
}
