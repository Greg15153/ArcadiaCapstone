namespace API.Models
{
    using System;
    using System.Collections.Generic;
    using System.ComponentModel.DataAnnotations;
    using System.ComponentModel.DataAnnotations.Schema;
    using System.Data.Entity.Spatial;

    public partial class Survey
    {
        public Survey()
        {
            CompletedSurveys = new HashSet<CompletedSurvey>();
        }

        public int Id { get; set; }

        [StringLength(50)]
        public string Title { get; set; }

        public TimeSpan? Wait { get; set; }

        public virtual ICollection<CompletedSurvey> CompletedSurveys { get; set; }

        public virtual SurveyQuestion SurveyQuestion { get; set; }
    }
}
