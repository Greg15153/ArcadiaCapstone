namespace WebAPI.Models
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
            SurveyQuestions = new HashSet<SurveyQuestion>();
        }

        public int Id { get; set; }

        [Required]
        [StringLength(50)]
        public string Title { get; set; }

        public int Delay { get; set; }

        public virtual ICollection<CompletedSurvey> CompletedSurveys { get; set; }

        public virtual ICollection<SurveyQuestion> SurveyQuestions { get; set; }
    }
}
