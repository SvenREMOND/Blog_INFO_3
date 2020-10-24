var Button_rep = document.querySelectorAll('.form_comment');

var form = document.querySelector('.form__comments');

for(let i = 0; i<Button_rep.length; i++){

    Button_rep[i].addEventListener("click", function(){
        var id_com_parent = Button_rep[i].getAttribute("id")

        form.querySelector('.com_parent').setAttribute("value", id_com_parent)
    })

}