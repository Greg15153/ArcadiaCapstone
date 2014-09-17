namespace API.Models
{
    using System;
    using System.Collections.Generic;
    using System.ComponentModel.DataAnnotations;
    using System.ComponentModel.DataAnnotations.Schema;
    using System.Data.Entity.Spatial;

    public partial class Question
    {
        public Question()
        {
            SurveyQuestions = new HashSet<SurveyQuestion>();
        }

        public int Id { get; set; }

        [Required]
        public string Type { get; set; }

        [Column("Question")]
        [Required]
        public string Question1 { get; set; }

        public virtual ICollection<SurveyQuestion> SurveyQuestions { get; set; }
    }
}
