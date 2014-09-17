namespace API.Models
{
    using System;
    using System.Collections.Generic;
    using System.ComponentModel.DataAnnotations;
    using System.ComponentModel.DataAnnotations.Schema;
    using System.Data.Entity.Spatial;

    public partial class Result
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.None)]
        public int CompletedSurveyId { get; set; }

        public int QuestionId { get; set; }

        [Required]
        public string Answer { get; set; }

        public virtual CompletedSurvey CompletedSurvey { get; set; }
    }
}
