import React, { useState } from "react";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import {
  File,
  PlusCircle,
  Search,
  Pencil,
  Trash,
  MoreHorizontal,
  ListFilter,
} from "lucide-react";
import { Loader2 } from "lucide-react";

import { useNavigate } from "react-router-dom";
import { toast } from "sonner";
import { Button } from "@/components/ui/button";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import axios from "axios";

const Delete = ({ id }) => {
  const user = JSON.parse(localStorage.getItem("user"));
  const token = user.token;
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(false);
  const queryClient = useQueryClient();
  const deleteMutation = useMutation({
    mutationFn: async () => {
      const response = await axios.delete(
        `http://127.0.0.1:8000/api/devtas/${id}`,
        {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`, // Include the Bearer token
          },
        }
      );
      return response.data;
    },
    onSuccess: (data) => {
      queryClient.invalidateQueries("users");
      toast.success("Devta Deleted Successfully");
      setIsLoading(false);
      navigate("/devtas");
    },
    onError: (error) => {
      setIsLoading(false);

      console.log("got error ", error);
    },
  });
  const onDelete = () => {
    setIsLoading(true);
    deleteMutation.mutate();
  };

  return (
    <div>
      <AlertDialog>
        <AlertDialogTrigger asChild>
          <Button variant="ghost" size="sm" className="w-full text-sm">
            <Trash /> Delete
          </Button>
        </AlertDialogTrigger>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
            <AlertDialogDescription>
              This action cannot be undone. By proceeding, you will permanently
              delete your "Devta details". Once deleted, this information cannot
              be recovered.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction onClick={() => onDelete()}>
              {isLoading ? (
                <>
                  <Loader2 className="animate-spin mr-2" /> {/* Spinner */}
                  loading...
                </>
              ) : (
                "Continue"
              )}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  );
};

export default Delete;