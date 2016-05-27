<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
    <td colspan="2" align="center">
        <div id="new_question" style="display:block;">
            <form name="addquestion" method="POST" action="<? echo $scriptenv; ?>?op=add_question">
                <table>
                    <tr>
                        <td>Question :<br>
                            <textarea id="new_question" name="new_question" cols="80" rows="10"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <input type="submit" value="ENVOYER">
                        </td>
                    </tr>
                </table>
                <script language="javascript">
                document.addquestion.new_question.focus();
                </script>
            </form>
        </div>
    </td>
</tr>
</table>
